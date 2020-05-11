jQuery(document).ready(function($) {
	var editor, editorVariabes, editorVarCustom;
	// export
	$('.btn-action[data-action="tool.export"]').click(function() {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=export&id=' + tempId;
		// get groups to export
		if ($('#tool-export-groups').val()) {
			var groups = $('.tool-export-groups-wrap input:checked').map(function() {return this.value}).toArray();
			if (!groups.length) {
				alert(T4Admin.langs.toolExportNoSelectedGroupsWarning);
				return;
			}
			url += '&groups=' + groups.join(',');
		}
		location.href = url;
	})

	$('#tool-export-groups').on('change', function() {
		var val = $(this).val();
		if (val) {
			// selected, show groups with uncheck all
			$('.tool-export-groups-wrap').show().find('input').prop('checked', false);
		} else {
			$('.tool-export-groups-wrap').hide();
		}
	})


	// handle file selected for import
	$('input[name="tool-import-file"]').on('change', function (e) {
		var reader = new FileReader();
		reader.input = this;
        reader.onload = confirmImport;
        reader.readAsDataURL(this.files[0]);
	})


	$('#tool-import-preset').on('change', function () {
		// select a preset, then load the preset content
		var preset = $(this).val();
		clearImport();
		if (!preset) return;
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=getPreset&name=' + preset + '&id=' + tempId;
		$.ajax(url).then(function(data) {
			proccessImportData (data);
		})
	})


	var confirmImport = function (e) {
		var content = e.target.result.split('base64,'),
			rawData = atob(content[1]),
			data = JSON.parse(rawData);
		proccessImportData (data);
	}


	var proccessImportData = function (data) {
		if (!data) {
			clearImport();
			alert(T4Admin.langs.toolImportDataFileError);
			return;
		}

		// find all group and check if group data available
		var count = 0;
		$('.tool-import-form [type="checkbox"]').each(function(){
			var $group = $(this),
				group = $group.val(),
				available = false;

			for(var name in data) {
				if ((name == group || name.startsWith(group + '_')) && data[name] != "") {
					available = true;
					count++;
					break;
				}
			}

			if (available) {
				$group.prop('checked', true).prop('disabled', false).closest('li').removeClass('disabled');
			} else {
				$group.prop('checked', false).prop('disabled', true).closest('li').addClass('disabled');
			}
		})

		if (count) {
			$('.tool-import-form').show();

			// bind action event
			$('.t4-btn[data-action="tool.import"]').off('click').on('click', function() {
				doImport(data);
			})
		} else {
			alert(T4Admin.langs.toolImportDataFileEmptyWarning);
			$('.tool-import-form').hide();
			return;
		}
	}

	var doImport = function (data) {
		var groups = $('.tool-import-form [type="checkbox"]').filter(':enabled:checked').map(function(){return this.value});
		for(var i=0; i<groups.length; i++) {
			var group = groups[i];
			for(var name in data) {
				if (name == group || name.startsWith(group + '_')) {
					updateValue(name, data[name]);
				}
			}
		}

		// clear form
		finishImport();
	}

	var updateValue = function (name, val) {
		$('[name="jform[params][' + name + ']"]').val(val);
		if(name == 't4layout'){
			var style_name = $('[name="jform[params][t4-layout]"]').val();
		}
		if(name == 'navigation_mega_settings'){
			T4AdminMegamenu.updateMegamenu();
		}
	}

	var finishImport = function () {
		alert(T4Admin.langs.toolImportDataDone);

		clearImport();
	}

	var clearImport = function () {
		$('input[name="tool-import-file"]').val('');
		$('.tool-import-form').hide();
	}

	var initCssEditor = function(editor,data){
		if(!editor){
			editor = CodeMirror.fromTextArea(data,{
				lineNumbers: true,
				mode: "css",
				autofocus: true,
				tabsize: 2,
				firstLineNumber: 1
			});
		}else{
			editor.getDoc().setValue(data);
		}
		setTimeout(function() {
		   editor.refresh();
		},1);
	}


	// Edit custom css
	var $cssmodal = $('.t4-css-editor-modal');
	if (!$cssmodal.parent().is('.themeConfigModal')) $cssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.css"]').click(function() {
		// load current custom css
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=getcss&id=' + tempId;
		$.ajax(url).then(function(css) {
			// Show edit popup with current css
			$('body').addClass('t4-modal-open');
			$('#t4_code_css').text(css);
			var textArea = $('#t4_code_css').get(0);
			if(!editor){
				editor = CodeMirror.fromTextArea(textArea,{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editor.getDoc().setValue(css);
			}
			setTimeout(function() {
			   editor.refresh();
			},1);

			$cssmodal.show();

		})
	})
	$('body').on('click','.t4-css-editor-apply', function(e) {
	    var css = editor.getDoc().getValue("\n");
	    saveCss(css);
	});
	var saveCss = function (css) {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=savecss&id=' + tempId;
		$.post(url, {css: css}).then(function() {
			// Save done, show message and reload preview
			$cssmodal.hide();
			$('body').removeClass('t4-modal-open');
			$(document).trigger('reload-preview');
			T4Admin.Messages(T4Admin.langs.customCssSaved,'message');
		})
	}


	// SCSS TOOLS
	var $scssmodal = $('#t4-tool-scss-modal');
	$scssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.scss"]').click(function() {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=load&id=' + tempId;
		$.ajax(url).then(function(data) {
			$('#t4-scss-editor-variables').text(data.variables);
			$('#t4-scss-editor-custom').text(data.custom);
			if(!editorVariabes){
				editorVariabes = CodeMirror.fromTextArea($('#t4-scss-editor-variables').get(0),{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editorVariabes.getDoc().setValue(data.variables);
			}
			setTimeout(function() {
			   editorVariabes.refresh();
				},1);
			if(!editorVarCustom){
				editorVarCustom = CodeMirror.fromTextArea($('#t4-scss-editor-custom').get(0),{
					lineNumbers: true,
					mode: "css",
					autofocus: true,
					tabsize: 2,
					direction: (document.dir == 'rtl')  ? "rtl" : "ltr",
					firstLineNumber: 1
				});
			}else{
				editorVarCustom.getDoc().setValue(data.custom);
			}
			setTimeout(function() {
			   editorVarCustom.refresh();
			},1);
			$scssmodal.show();
		});
	})

	$scssmodal.on('click', '.btn[data-action="apply"]', function(e) {
		T4Admin.Messages('Saving & Compiling ...', 'status');
		var scssVar = editorVariabes.getDoc().getValue("\n");
		var scssCustom = editorVarCustom.getDoc().getValue("\n");
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=save&id=' + tempId;
		$.post(url, {
			variables: scssVar,
			custom: scssCustom
		}).then(function(resp) {
			if (resp.success == false) {
				T4Admin.Messages(resp.message, 'error');
			} else {
				T4Admin.Messages('Save & compile successfully!');
				// $scssmodal.hide();
			}
		})
	})
	$scssmodal.on('click','.nav-tabs li', function(e) {
		if(!$scssmodal.is(":hidden")){
			setTimeout(function() {
			   editorVariabes.refresh();
			},1);
			setTimeout(function() {
			   editorVarCustom.refresh();
			},1);
		}
	});
	$scssmodal.on('click', '.btn[data-action="clean"]', function(e) {
		T4Admin.Messages('Removing Local css ...', 'status');
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=clean&id=' + tempId;
		$.post(url, {}).then(function(resp) {
			if (resp.error) {
				T4Admin.Messages(resp.error, 'error');
			} else {
				T4Admin.Messages('Remove successfully!')
			}
		})
	})


})
