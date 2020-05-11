(function($){
    "use strict";
    $(document).ready(function() {
        var initUserColor = function(){
            $('.choose-color-pattern').find('.user-color').remove();
            var userColor = $('.custom-color-list').find('.control-group'),$userColorHtml = '';
            userColor.each(function(){
                var data = $(this).data();
                var colors = $(this).find('input.custom-color-item').val();
                $userColorHtml += '<li class="t4-select-pattern user-color" data-val="'+data.class+'" data-color="'+colors+'" data-name="'+data.name+'">';
                $userColorHtml += '<span class="preview-icon" data-bgcolor="'+colors+'" style="background-color: '+colors+';"></span>';
                $userColorHtml += '<span class="color-label">'+data.name+'</span>';
                $userColorHtml += '</li>';
            });
            $('.choose-color-pattern').find('ul').append($userColorHtml);
        }
        var generatedPalettes = function(trigger){
            var colorPattern = {},$pattern;
            if(trigger){
                $pattern = '.pattern';
            }else{
                $pattern = '.pattern.active';
            }
            $('.pattern-list').find($pattern).not('.none').each(function(index){
                var dataColorPt = $(this).data();
                colorPattern[dataColorPt.class] = dataColorPt;
            });
            return colorPattern;
        }
        var updateToJson = function(trigger){
            var data = generatedPalettes(trigger);
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
            $.post(url, {task: 'save',all:trigger,value: data}).then(function(response) {
                if (response.ok) {
                    T4Admin.Messages(T4Admin.langs.palettesUpdated,'message');
                } else {
                    T4Admin.Messages(response.error,'error');
                }
            })
        }
	    $('body').on('click','.pt-color-edit', function(e) {
            e.preventDefault();
            $('.t4-row-settings').hide();
            // Show edit popup with current palettes
            var $plmodal = $('.t4-palettes-modal');
            if (!$plmodal.parent().is('.themeConfigModal')) $plmodal.appendTo($('.themeConfigModal'));
            //init user color to chose color
            initUserColor();
            $plmodal.show();
            var $parentColor = $(this).parents('.pattern'),$data = $parentColor.data(),$data_color = {};
            $('.pattern.active').removeClass('active');
            $parentColor.addClass('active');
            $('.t4-patterns-inner').find('.config_pattern').find('.t4-pattern').each(function(){
                $(this).parents('.controls').find('li').removeClass('active');
        		var attrName = $(this).data('attrname'),$value = "none",$dataVal = "";
                if(attrName != 'title'){
                    if($data[attrName].search('#') != -1){
                        $data_color[attrName] = $data[attrName];
                         $value = $data[attrName];
                         $dataVal = $data[attrName];
                    }else{
                        if($(this).closest('.controls').find('.choose-color-pattern').find('li[data-val="'+$data[attrName]+'"]').length){
                            $value = $(this).closest('.controls').find('.choose-color-pattern').find('li[data-val="'+$data[attrName]+'"]').data('name');
                            $data_color[attrName] = $(this).closest('.controls').find('.choose-color-pattern').find('li[data-val="'+$data[attrName]+'"]').data('color');
                        }
                        $dataVal = $data[attrName].replace(/_/g," ");
                    }
                    if($(this).parents('.controls').find('li[data-val="'+$data[attrName]+'"]').length){
                        $(this).parents('.controls').find('li[data-val="'+$data[attrName]+'"]').addClass('active');
                    }else{
                         $(this).parents('.controls').find('li[data-val="none"]').addClass('active');
                    }
                    if(typeof $data_color[attrName] == "undefined") $data_color[attrName] = 'inherit';
                    $(this).parents('.color-preview').find('.preview-icon').css({background:$data_color[attrName]});
                    
                    $(this).val($value);
                    $(this).data('val',$dataVal);

                }
                if(attrName == 'title') $(this).val($data[attrName]);
                $(this).data('val',$data[attrName]);
            });
            $('.pattern-preview').css({background:$data_color.background_color});
            $('.pattern-preview .text_color').css({color:$data_color.text_color});
            $('.pattern-preview .link_color').css("color", $data_color.link_color);
            $('.pattern-preview .link_color').data('link_hover_color',$data_color.link_hover_color);
            $('.pattern-preview .link_color').data('link_color',$data_color.link_color);
            $('.pattern-preview .link_color').hover(function(){
                $(this).css("color", $data_color.link_hover_color);
            }, function(){
                $(this).css("color", $data_color.link_color);
            });
        });
        $('body').on('change','.t4-pattern', function(e) {
            var $data = $(this).data(),attrName = $data.attrname,$val = $data.val,$color = $data.color,$hex_color = $(this).val();
            if($hex_color.search('#') != -1){
               $color = $hex_color;
               $(this).data('val',$hex_color);
            }
            if(attrName == 'background_color'){
               $('.pattern-preview').css({background:$color});
            }else{
               $('.pattern-preview .'+attrName).css({color:$color});
            }
            if(attrName == 'link_color' || attrName == 'link_hover_color'){
                $('.pattern-preview .link_color').data(attrName,$color);
            }
        });
        $('body').on('mouseover','.pattern-preview .link_color', function(e) {
            var $data = $(this).data();
            $(this).css("color", $data.link_hover_color);
        });
        $('body').on('mouseout','.pattern-preview .link_color', function(e) {
             var $data = $(this).data();
            $(this).css("color", $data.link_color);
        });
    	$('body').on('click','.pt-color-cancel', function(e) {
    	    e.preventDefault();
            $('.pattern.active').removeClass('active');
    	    $('.config_pattern').slideUp('slow');
    	});
    	$('body').on('click','.pt-color-create', function(e) {
    	    e.preventDefault();
            var $parentColor = $(this).closest('.pattern'), $patternClone = $parentColor.clone(true), $data = $patternClone.data();
            var $val = $(this).val(), $name_palette = [];
            $('.pattern-list').find('.pattern').each(function(){
                $name_palette.push($(this).data('title'));
            });
            $patternClone.removeClass($data.class);
            var random = '', newTitle = '', title = $data.title;
            for (var i = 0; i < 100; i++) {
                random = ' copy '+i;
                if(i==0) random = ' copy';
               newTitle = title + random;
               if($name_palette.indexOf(newTitle) == -1){
                break;
               }
            }
            $patternClone.addClass($data.class+random.replace(/\s/g,'_'));
            $patternClone.addClass('clone');
            // $patternClone.data('class','');
            $patternClone.find('.pattern-title').text(newTitle);
            $patternClone.data('title',newTitle);
            //update status action
            var action_del = $patternClone.find('.pt-color-del');
            if(action_del.is(":hidden")){
                action_del.parents('li').removeClass('hidden');
            }
            action_del.parents('li').html('<a class="pt-color-del" href="#" data-tooltip="Delete"><i class="fal fa-trash-alt fa-fw"></i></a>');
            $patternClone.insertAfter($parentColor);
            $patternClone.find('.pt-color-edit').trigger('click');
    	});

    	$('body').on('click','.t4-patterns-apply', function(e) {
    	    e.preventDefault();
            var $patternActive = $('.pattern.active'),$triggerSaveAll = false;
            if($patternActive.hasClass('clone')) {
                $patternActive.removeClass('clone');
                $patternActive.data('class','');
                $triggerSaveAll = true;
            }
            $('.t4-patterns-inner').find('input.t4-pattern').each(function(){
                var $data = $(this).data(), $attrName = $data.attrname,$val = $data.val,$dataColor = '';
                $patternActive.removeData($attrName);
                if($attrName == 'title'){
                    $val = $(this).val();
                    if($patternActive.data('class') == ''){
                        var $classes = $val.replace(/\s+/g,"_").toLowerCase();
                        $patternActive.data('class',$classes);
                    }
                    $patternActive.find('.pattern-title').text($val);
                }else{
                    if($val.search('#') != -1){
                        $dataColor = $val;
                    }else{
                        $dataColor= $('li[data-val="'+$val+'"]').data('color');
                    }
                    $patternActive.find('span.'+$attrName).css({background:$dataColor});
                }
                $patternActive.data($attrName,$val);
            });
            //update status action
            var action_del = $patternActive.find('.pt-color-del');
            if(action_del.is(":hidden")){
                action_del.parents('li').removeClass('hidden');
            }

            $(this).closest('.t4-palettes-modal').hide();
            $('.t4-row-settings').show();
            updateToJson($triggerSaveAll);
    	});
        $('body').on('click','.t4-patterns-cancel', function(e) {
            $(this).closest('.t4-palettes-modal').hide();
            var $pattern = $('.pattern.active'),dataClass = $pattern.data('class');
            if($pattern.hasClass('clone')){
                $pattern.remove();
                $(document).find('.pattern[data-class="'+dataClass+'"]').addClass('active');
            }
            $('.t4-row-settings').show();
        });
        $(document).on('click','.pattern-actions-list', function(e) {
             e.stopPropagation();
        });
		$('body').on('click','.pt-color-del', function(e) {
            e.stopPropagation();
    	    e.preventDefault();
            var $langs = 'colorPalettesConfirm'+$(this).data('tooltip');
            var $message = 'colorPalettes'+$(this).data('tooltip');
            var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Palettes&id=' + tempId;
            var plEl = $(this).closest('.pattern'),plName = plEl.data('class'),plDel = $(this).parents('li');
            if(plEl.hasClass('clone')){
                plEl.remove();
                T4Admin.Messages(T4Admin.langs.palettesRemnoveClone,'message');
                return true;
            }
            T4Admin.Confirm(T4Admin.langs[$langs],function(ans){
              if (ans) {
                $.post(url, {task: 'remove',name: plName}).then(function(response) {
                    if (response.ok) {
                        var resData = response.data;
                        var resDataColor = response.datacolor;
                        if(!resData){
                            plEl.remove();
                        }else{
                            var plObj = plEl.find('li');
                            plObj.each(function(){
                                var elClass = $(this).find('span').attr('class');
                                if(elClass){
                                    plEl.data(elClass,resData[elClass]);
                                    if(!resDataColor[elClass]) resDataColor[elClass] = '';
                                    $(this).find('span').css({'background':resDataColor[elClass]})
                                }
                            });
                            plDel.addClass('hidden');
                        }
                        T4Admin.Messages(T4Admin.langs[$message],'message');
                    } else {
                        T4Admin.Messages(response.error,'error');
                    }
                })
              }else {
                 return false;
              }
            },'');

    	});
        $('body').on('click','.t4-pattern, .fa-angle-down', function(e) {
            $('.choose-color-pattern').addClass('hidden');
            $(this).closest('.controls').find('.choose-color-pattern').removeClass('hidden');
        });
        $('body').on('click','.t4-input-color', function(e) {
            $('.choose-color-pattern').addClass('hidden').removeClass('is-focus');
            //init user color to chose color
            initUserColor();
            var $colorActive = $(this).data('val');
            $(this).closest('.controls').find('.choose-color-pattern').find('.t4-select-pattern[data-val="'+$colorActive.replace(/\s+/g,'_')+'"]').addClass('active');
            $(this).closest('.controls').find('.choose-color-pattern').removeClass('hidden').addClass('is-focus');
        });
         $(document).on('click',function(e){
            if(!$('.t4-pattern-row').is(":hidden")){
                if(($('.fa-angle-down').index(e.target) == -1) && ($('.t4-pattern').index(e.target) == -1) && ($('.choose-color-pattern').index(e.target) == -1)){
                    $('.choose-color-pattern').addClass('hidden');
                }
            }else{
                if($('.choose-color-pattern').hasClass("is-focus")){
                    if(($('.t4-input-color').index(e.target) == -1) && ($('.choose-color-pattern').index(e.target) == -1)){
                        $('.choose-color-pattern').addClass('hidden').removeClass('is-focus');
                    }
                }
            }
        });

        $('body').on('click','.t4-select-pattern', function(e) {
            var $val = $(this).data('val'),$input = $(this).closest('.controls').find('input.t4-pattern');
            if($input.length == 0) $input = $(this).closest('.controls').find('input.t4-input-color');
            $(this).closest('.controls').find('li').removeClass('active');
            $(this).addClass('active');
            var $value = $val.replace(/_/g," ");
            var dataName = $(this).data('name');
            $input.val(dataName);
            $input.data('val',$val);
            $input.prev().css({'background-color':$(this).data('color')});
            $input.data('color',$(this).data('color'));
            $input.trigger('change');
            $(this).closest('.controls').find('.choose-color-pattern').addClass('hidden');
         });
        $(document).on('change','.group_brand_colors input',function(){
            var attrId = this.id,$val = $(this).val();
            var nameField = attrId.replace('typelist_theme_','');
            var $input_color = $(document).find('.t4-input-color');
            var palettes_color = $(document).find('.t4-select-pattern[data-val="'+nameField+'"]');
            palettes_color.data('color',$val);
            palettes_color.find('.preview-icon').css({'background-color':$val});
            $input_color.each(function(){
                var $input = $(this), $input_val = $input.data('val');
                if($input.data('val').replace(/ /g,'_') == nameField){
                    $input.closest('.color-preview').find('.preview-icon').data('bgcolor',$val);
                    $input.closest('.color-preview').find('.preview-icon').css({'background-color':$val});
                }
            })
            
        });
        //input color config
        $(document).on('user-colors-update', initUserColor);
        //trigger update palettes color to JSON
        $(document).on('palettestoJson', updateToJson);
	});

})(jQuery);
