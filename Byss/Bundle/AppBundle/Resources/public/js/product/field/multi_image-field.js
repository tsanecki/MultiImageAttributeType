'use strict';
/**
 * Media field
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define([
        'jquery',
        'pim/field',
        'underscore',
        'routing',
        'pim/attribute-manager',
        'byss/template/product/field/multi_image',
        'pim/dialog',
        'oro/mediator',
        'oro/messenger',
        'pim/media-url-generator',
        'jquery.slimbox'
    ],
    function ($, Field, _, Routing, AttributeManager, fieldTemplate, Dialog, mediator, messenger, MediaUrlGenerator) {
        return Field.extend({
            fieldTemplate: _.template(fieldTemplate),
            events: {
                'change .edit .byss-multi-image:first input[type="file"]': 'updateModel',
                'click  .remove': 'removeImage',
            },
            uploadContext: {},
            renderInput: function (context) {
                return this.fieldTemplate(context);
            },
            getTemplateContext: function () {
                return Field.prototype.getTemplateContext.apply(this, arguments)
                    .then(function (templateContext) {
                        templateContext.inUpload          = !this.isReady();
                        templateContext.mediaUrlGenerator = MediaUrlGenerator;

                        return templateContext;
                    }.bind(this));
            },

            renderCopyInput: function (value) {
                return this.getTemplateContext()
                    .then(function (context) {
                        var copyContext = $.extend(true, {}, context);
                        copyContext.value = value;
                        copyContext.context.locale    = value.locale;
                        copyContext.context.scope     = value.scope;
                        copyContext.editMode          = 'view';
                        copyContext.mediaUrlGenerator = MediaUrlGenerator;

                        return this.renderInput(copyContext);
                    }.bind(this));
            },
            updateModel: function () {
                let input = this.$('.edit .byss-multi-image:first input[type="file"]').get(0);
                let preview = this.$('.edit .byss-multi-image:first .preview').get(0);
                let progressbar = this.$('.edit .byss-multi-image:first .progress');


                let files = input.files;
                let filesLength = files.length;
                for (let i = 0; i < filesLength; i++) {
                    let f = files[i];
                    let fileReader = new FileReader();
                    fileReader.onload = (function(e) {
                        $('<span class="pip">' +
                          '<img class="imageThumb" src="' + e.target.result + '" data-val="\' + e.target.result + \'" title="' + f.name + '"/><br/>' +
                           '<span class="AknButtonList-item AknIconButton AknIconButton--grey clear-field remove">' +
                           '<i class="icon icon-trash"></i></span>' +
                            '</span>').appendTo(preview);
                    });
                    fileReader.readAsDataURL(f);

                    progressbar.css({opacity: 1});
                    progressbar.css({width: (((i + 1) / filesLength) * 100) + '%'});

                    let formData = new FormData();
                    formData.append('file', f);
                    this.uploadContext = {
                        'locale': this.context.locale,
                        'scope':  this.context.scope
                    };

                    $.ajax({
                        url: Routing.generate('pim_enrich_media_rest_post'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                    })
                        .done(function (data) {
                            this.setUploadContextValue(data);
                        }.bind(this))
                        .fail(function (xhr) {
                            var message = xhr.responseJSON && xhr.responseJSON.message ?
                                xhr.responseJSON.message :
                                _.__('pim_enrich.entity.product.error.upload');
                            messenger.enqueueMessage('error', message);
                        })
                        .always(function () {
                            progressbar.css({opacity: 0});
                            progressbar.css({width: 0});
                            this.setReady(true);
                        }.bind(this));
                }
            },
            removeImage: function(e) {
                let productValue = AttributeManager.getValue(
                    this.model.get('values'),
                    this.attribute,
                    this.uploadContext.locale,
                    this.uploadContext.scope
                );

                let target = $(e.target).closest('.pip').find('img');
                let filename = target.attr('data-val');
                target = $(e.target).closest('.pip');
                target.remove();

                productValue.data = productValue.data.filter(function(value, index, arr) {
                    return (value !== filename);
                });

                mediator.trigger('pim_enrich:form:entity:update_state');
            },
            setUploadContextValue: function (value) {
                var productValue = AttributeManager.getValue(
                    this.model.get('values'),
                    this.attribute,
                    this.uploadContext.locale,
                    this.uploadContext.scope
                );

                if (!(productValue.data instanceof Array)) {
                    productValue.data = new Array(productValue.data);
                }

                productValue.data.push(value);

                mediator.trigger('pim_enrich:form:entity:update_state');
            }
        });
    }
);
