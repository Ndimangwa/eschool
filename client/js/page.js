/*My Global javascript function*/
function _(_ele)	{
	return document.getElementById(_ele);
}
function _t(_text)	{
	return document.createTextNode(_text);
}
var entityMap = {
    "&": "%26",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '%2F',
	"=": '%3D'
  };
$(document).bind('keypress', function(e)	{
	if (e.which == 13)	{
		//Enter Key is pressed 
		$('a.click-to-search').trigger('click');
		$('input.button-login').trigger('click');
	}
});
function escapeHtml(string) {
   return String(string).replace(/[&<>"'\/=]/g, function (s) {
     return entityMap[s];
   });
 }
function generalFormValidation(command1, _form, _target)	{
	if (! validateEditTextFieldLength(command1, _form, _target)) return false;
	var form1 = document.getElementById(_form);
	var target1 = document.getElementById(_target);
	if (! (command1 && form1 && target1)) return false;
	var bln = true;
	var list1 = form1.getElementsByTagName("input");
	if (list1)	{
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	list1 = form1.getElementsByTagName("textarea");
	if (list1) {
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	list1 = form1.getElementsByTagName("select");
	if (list1) {
		for (var i=0; i < list1.length; i++) bln = bln && Validation.validate(list1[i], target1);
	}
	return bln;
}
function generalFormSubmission(command1, _form, _target)	{
	var bln = generalFormValidation(command1, _form, _target);
	if (bln)	{
		_(_form).submit();
	}
	return bln;
}
(function($)	{
	/*Begin of Widget Creation*/
	$.widget("ab.timetableTooltip", $.ui.tooltip,	{
		options:	{
			zg: true
		},
		_create: function()	{
			this._super();
			this.options.content = $.proxy(this, "_content");
		},
		_content: function()	{
			window.alert("Hapa Nipo");
			return "I like this";
		}
	});
	$.widget( "app.checkbox", {
    
        _create: function() {

            // Call the default widget constructor first.            
            this._super();
            
            // Hide the HTML checkbox, then insert our button.
            this.element.addClass( "ui-helper-hidden-accessible" );
            this.button = $( "<button/>" ).insertAfter( this.element );
            
            // Configure the button by adding our widget class,
            // setting some default text, default icons, and such.
            // The create event handler removes the title attribute,
            // because we don't need it.
            this.button.addClass( "ui-checkbox" )
                       .text( "checkbox" )
                       .button({
                           text: false,
                           icons: { 
                               primary: "ui-icon-blank"
                           },
                           create: function( e, ui ) {
                               $( this ).removeAttr( "title" ); 
                           }
                       });
            
            // Listen for click events on the button we just inserted and
            // toggle the checked state of our hidden checkbox.
            this._on( this.button, {
                click: function( e ) {
                    this.element.prop( "checked", !this.element.is( ":checked" ) );
                    this.refresh();
                }
            });
            
            // Update the checked state of the button, depending on the
            // initial checked state of the checkbox.
            this.refresh();
            
        },
        
        _destroy: function() {
            
            // Standard widget cleanup.
            this._super();
            
            // Display the HTML checkbox and remove the button.
            this.element.removeClass( "ui-helper-hidden-accessible" );
            this.button.button( "destroy" ).remove();
        
        },
        
        refresh: function() {
            // Set the button icon based on the state of the checkbox.
            this.button.button( "option", "icons", {
                primary: this.element.is( ":checked" ) ?
                "ui-icon-check" : "ui-icon-blank"
            });

        }
    
    });
	/*End of Widget Creation*/
	window.showImageToBeUploaded = function(_fileSource, _imageDestination)	{
		/*
		_fileSource the jQuery rules to get the fileSource 
		_imageDestination the jQuery rules to get the destination image 
		*/
		window.alert("ONE");
		$fileSource1 = $(_fileSource);
		$imageDestination1 = $(_imageDestination);
		if (! $fileSource1.length) return;
		if (! $imageDestination1.length) return;
		var fileReader1 = new FileReader();
		var selectedFiles = $fileSource1[0].files;
		if (selectedFiles == undefined || selectedFiles.length == 0) return;
		$fileReader1 = $(fileReader1);
		if (! $fileReader1.length) return;
		$fileReader1.on('load', function(event)	{
			$(this).addClass('load-event-bounded');
			window.alert("Results "+ this.result);
			$imageDestination1[0].src = this.result;
			//$imageDestination1.prop('src', this.result);
		});
		$fileSource1.on('change', function(event)	{
			$(this).addClass('change-event-bounded');
			fileReader1.readAsDataURL(selectedFiles[0]);
		});
	}
	window.uiSortableContainerDataMaintainaceAfterMoveUpDown = function(containerSelector)	{
		var $container1 = $(containerSelector);
		if (! $container1.length) return;
		var controlIndexPrefix = $container1.data('controlIndexPrefix');
		var $trList1 = $container1.find('table.ui-sortable-table tr.ui-sortable-row');
		if (! $trList1.length) return;
		$trList1.each(function(index, row)	{
			var $row1 = $(row);
			var $controlIndexVariable1 = $row1.find('input.sortable-control');
			var variableName = controlIndexPrefix + "[" + index + "]";
			$controlIndexVariable1.prop('name', variableName);
			$row1.find('button.ui-sortable-button').button('option', 'label', index+ 1);
		});
	}
	window.uiSortableContainerMaintainance = function(containerSelector)	{
		var $container1 = $(containerSelector);
		if (! $container1.length) return;
		var $table1 = $container1.find('table.ui-sortable-table');
		if (! $table1.length) return;
		var $controls1 = $container1.find('div.ui-sortable-controls');
		if (! $controls1.length) return;
		//Check Clear Selection hide/unhide 
		var $clearSelection1 = $controls1.find('a.ui-sys-clear');
		if (! $clearSelection1.length) return;
		//Up Down Controls 
		var $upControl1 = $controls1.find('a.ui-sys-move-up');
		if (! $upControl1.length) return;
		var $downControl1 = $controls1.find('a.ui-sys-move-down');
		if (! $downControl1.length) return;
		if ($upControl1.hasClass('ui-sys-hidden')) $upControl1.removeClass('ui-sys-hidden');
		if ($downControl1.hasClass('ui-sys-hidden')) $downControl1.removeClass('ui-sys-hidden');
		if ($table1.find('tr.ui-sys-sortable-selected').length)	{
			if ($clearSelection1.hasClass('ui-sys-hidden')) $clearSelection1.removeClass('ui-sys-hidden');
			if ($upControl1.hasClass('ui-sys-hidden')) $upControl1.removeClass('ui-sys-hidden');
			if ($downControl1.hasClass('ui-sys-hidden')) $downControl1.removeClass('ui-sys-hidden');
		} else {
			if (! $clearSelection1.hasClass('ui-sys-hidden')) $clearSelection1.addClass('ui-sys-hidden');
			if (! $upControl1.hasClass('ui-sys-hidden')) $upControl1.addClass('ui-sys-hidden');
			if (! $downControl1.hasClass('ui-sys-hidden')) $downControl1.addClass('ui-sys-hidden');
		}//end-if-else
		var foundSelectedPosition = -11;
		var $rowList1 = $table1.find('tr.ui-sortable-row');
		$rowList1.each(function(index, row)	{
			var $row1 = $(row);
			if ($row1.hasClass('ui-sys-sortable-selected')) {
				foundSelectedPosition = index;
			}
		});
		//Making Decision 
		if (foundSelectedPosition == 0)	{
			//Disable Up
			if (! $upControl1.hasClass('ui-sys-hidden')) $upControl1.addClass('ui-sys-hidden');
		} else if (foundSelectedPosition == ($rowList1.length - 1))	{
			//Disable Down 
			if (! $downControl1.hasClass('ui-sys-hidden')) $downControl1.addClass('ui-sys-hidden');
		}
	}
	window.characterReplaceInAString = function(string1, char1, _pos)	{
		/*
		This function will replace a character in string1, by character char1, in position pos1 [zero based index]
		*/
		var pos1 = parseInt(_pos);
		if (pos1 >= string1.length) return;
		var str1 = "";
		for (var i=0; i < pos1; i++)	{
			/*characters before this position*/
			str1 = str1 + string1.charAt(i);
		}
		/* Now the character */
		str1 = str1 + char1;
		for (var i=(pos1 + 1); i < string1.length; i++)	{
			/* After this position */
			str1 = str1 + string1.charAt(i);
		}
		return (new String(str1)).trim().valueOf();
	}
	window.systemFirewallSaveAll = function(button1)	{
		var $button1 = $(button1); 
		var serverPath = $button1.data('serverPath');
		var contextCharacter = $button1.data('contextCharacter');
		var objectType = $button1.data('objectType');
		var objectId = $button1.data('objectId');
		var oldContextString = $button1.data('contextString');
		var $contextTarget1 = $('#' + $button1.data('contextTarget'));
		var nextPage = $button1.data('nextPage'); 
		var secureCode = $button1.data('secureCode');
		if (! $contextTarget1.length) return;
		//Prepare ContextString 
		var contextString = "";
		for (var i=0; i<oldContextString.length;i++) contextString = contextString + contextCharacter;
		if (oldContextString == contextString)	{
			$contextTarget1.empty();
			$('<span/>').text('Nothing has been updated').appendTo($contextTarget1);
			return;
		}
		//Proceed to Ajax Execution
		$.ajax({
			url: serverPath,
			method: "POST",
			data: { objecttype: objectType, objectid: objectId, contextstring: contextString, securecode: secureCode },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				//Successful 
				window.location.href = nextPage;
			} else	{
				//Failed 
				$contextTarget1.empty();
				$('<span/>').text(data.message).appendTo($contextTarget1);
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$contextTarget1.empty();
			$('<span/>').text(textStatus).appendTo($contextTarget1);
		}).always(function(data, textStatus, jqXHR)	{
			
		});
	}
	window.systemFirewallSaveCustom = function(button1)	{
		var $button1 = $(button1);
		var serverPath = $button1.data('serverPath');
		var objectType = $button1.data('objectType');
		var objectId = $button1.data('objectId');
		var secureCode = $button1.data('secureCode');
		var oldContextString = $button1.data('contextString');
		var $contextTarget1 = $('#' + $button1.data('contextTarget'));
		var nextPage = $button1.data('nextPage');
		if (! $contextTarget1.length) return; 
		//Prepare contextString
		var contextString = oldContextString;
		var $containingDiv1 = $button1.closest('div.ui-sys-search-results');
		if (! $containingDiv1.length) return;
		var $table1 = $containingDiv1.find('table.ui-sys-context-table');
		if (! $table1.length) return;
		$table1.find('tr.ui-sys-context-data-row').each(function(index, row1)	{
			var $row1 = $(row1);
			var contextCharacter = $row1.data('contextCharacter');
			var contextPosition = $row1.data('contextPosition');
			contextString = characterReplaceInAString(contextString, contextCharacter, contextPosition);
		});
		//Now we have our contextString
		if (oldContextString == contextString)	{
			$contextTarget1.empty();
			$('<span/>').text('Nothing has been updated').appendTo($contextTarget1);
			return;
		}
		//Proceed with AJax 
		$.ajax({
			url: serverPath,
			method: "POST",
			data: { objecttype: objectType, objectid: objectId, contextstring: contextString, securecode: secureCode },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				//Successful 
				window.location.href = nextPage;
			} else	{
				//Failed 
				$contextTarget1.empty();
				$('<span/>').text(data.message).appendTo($contextTarget1);
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$contextTarget1.empty();
			$('<span/>').text(textStatus).appendTo($contextTarget1);
		}).always(function(data, textStatus, jqXHR)	{
			
		});
	}
	window.isFieldValueAvailableForEditing = function(_tablename, _primarycolumnname, _primarycellvalue, _columnname, _cellvalue, _serverpath, _target)	{
		var isAvailable = false;
		var $target1 = $('#' + _target).empty();
		$.ajax({
			url: _serverpath,
			method: "POST",
			data: { tablename: _tablename, primarycolumnname: _primarycolumnname, primarycellvalue: _primarycellvalue, columnname: _columnname, cellvalue: _cellvalue },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				//Successful 
				isAvailable = true;
			} else	{
				//Failed 
				$('<span/>').text(data.message).appendTo($target1);
				isAvailable = false;
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$('<span/>').text(textStatus).appendTo($target1);
			isAvailable = false;
		}).always(function(data, textStatus, jqXHR)	{
			
		});
		return isAvailable;
	}
	window.isFieldValueAvailable = function(_tablename, _columnname, _cellvalue, _serverpath, _target)	{
		var isAvailable = false;
		var $target1 = $('#' + _target).empty();
		$.ajax({
			url: _serverpath,
			method: "POST",
			data: { tablename: _tablename, columnname: _columnname, cellvalue: _cellvalue },
			dataType: "json",
			cache: false,
			async: false
		}).done(function(data, textStatus, jqXHR)	{
			if (parseInt(data.code) === 0)	{
				//Successful 
				isAvailable = true;
			} else	{
				//Failed 
				$('<span/>').text(data.message).appendTo($target1);
				isAvailable = false;
			}
		}).fail(function(jqXHR, textStatus, errorThrown)	{
			$('<span/>').text(textStatus).appendTo($target1);
			isAvailable = false;
		}).always(function(data, textStatus, jqXHR)	{
			
		});
		return isAvailable;
	}
	window.validateEditTextFieldLength = function(button1, _form, _target)	{
		//you must pass a reference form 
		var $target1 = $('#' + _target);
		if (! $target1.length) return false;
		var $form1 = $('#' + _form);
		if (! $form1.length) return false;
		$target1.empty();
		//Ignore if not found source of length 
		var $parentList1 = $form1.find('.ui-sys-list-parent');
		var blnValidate = true;
		if ($parentList1.length)	{
			$parentList1.each(function(pli, plv)	{
				var $parent1 = $(plv);
				var maxLength = $parent1.attr('validate_length');
				if (! maxLength) maxLength = 0;
				var expression = "^(\\w|\\W){0," + maxLength + "}$";
				var errorMessage = $parent1.data('messageLength');
				var resultingString = "";
				var counter = 0;
				maxLength = parseInt(maxLength);
				var $editList1 = $parent1.find('.ui-sys-list-edit');
				if ($editList1.length)	{
					$editList1.each(function(eli, elv)	{
						//We expect one text field per container
						$textField1 = $(elv).find('input[type="text"]');
						if ($textField1.length)	{
							if (counter == 0)	{
								resultingString = $textField1.val();
							} else	{
								resultingString = resultingString + "," + $textField1.val();
							}
							counter++;
						}
					});
					/* We need to validate at this point */
					var regex1 = new RegExp(expression);
					if (! regex1) {
						$('<span>').text('Regular Expression has failed to execute')
									.appendTo($target1);
						blnValidate = false;
					}
					if (blnValidate)	{
						blnValidate = resultingString.match(regex1);
						if (! blnValidate)	{
							/*Display the appropriate message */
							$('<span/>').text(errorMessage)
									.appendTo($target1);						
						}
					}
				}
			});
		}
		return blnValidate;
	}
	window.getEditListCollections = function(prefix)	{
		prefix = prefix.replace("[", "_");
		prefix = prefix.replace("]", "_");
		return $('div.ui-sys-list-edit.' + prefix);
	}
	window.getEditListLength = function(prefix)	{
		//prefix ie churchpastortelephone 
		var $list1 = getEditListCollections(prefix);
		if (! $list1.length) return 0;
		return $list1.length;
	}
	window.updateFieldBlockCSV = function($fieldBlock1, blockIndex)	{
		if (! $fieldBlock1.length) return $fieldBlock1;
		//Update data-index 
		$fieldBlock1.attr('data-index', blockIndex); 
		//Update control naming and ids too 
		$fieldBlock1.find('fieldset.ui-sys-field-block-table tr.field-container').each(function(index, val)	{
			var $row1 = $(val); //same as $this 
			var rowIndex = $row1.attr('data-block-index'); 
			if (! rowIndex) return $fieldBlock1;
			var nextIndex = parseInt(blockIndex) + parseInt(rowIndex); //Each Row must have its own index 
			//Just do the necessary changes
			var $dataCellContainer1 = $row1.find('td.ui-sys-list-parent');
			if (! $dataCellContainer1.length) return; 
			$dataCellContainer1.attr('fieldvalue' + '[' + nextIndex + ']');
			$dataCellContainer1.find('input.hidden-storage').each(function(_i, _v)	{
				var $input1 = $(_v); //same as $this 
				var prefix = $input1.attr('data-storage-name-prefix');
				if (prefix) $input1.attr('name', prefix + '[' + nextIndex + ']');
				if (prefix == "namespaceTag")	$input1.val(blockIndex); //Entire block should have a separate space
			});
			$dataCellContainer1.find('.field-capturedata').each(function(_i, _v)	{
				var $dataControl1 = $(_v);
				$dataControl1.attr('name', 'fieldvalue' + '[' + nextIndex + ']');
			});
		});
		return $fieldBlock1;
	}
	window.updateFieldBlock = function($fieldBlock1, nextIndex)	{
		var disp_i = nextIndex + 1;
		if (! $fieldBlock1.length) return $fieldBlock1;
		//Update data-index 
		$fieldBlock1.attr('data-index', nextIndex); 
		//Update title 
		$fieldBlock1.find('span.ui-sys-field-block-title').html("Block " + disp_i);
		//Update control naming and ids too 
		$fieldBlock1.find('fieldset.ui-sys-field-block-table div.pure-control-group').each(function(index, val)	{
			var $row1 = $(val); //same as $this 
			var $label1 = $row1.find('label.data-prefix-label');
			var $text1 = $row1.find('input.data-text-control');
			var $select1 = $row1.find('select.data-select-control');
			if ($label1.length)	{
				var namePrefix = $label1.attr('data-name-prefix');
				$label1.attr('for', namePrefix + '_' + nextIndex + '_');
				if ($text1.length)	{
					$text1.attr('id', namePrefix + '_' + nextIndex + '_');
					$text1.attr('name', namePrefix + '[' + nextIndex + ']');
					$text1.attr('validate_message', "Block " + disp_i + ": " + $text1.attr('backup_validate_message'));
					$text1.val("");
				}
				if ($select1.length)	{
					$select1.attr('id', namePrefix + '_' + nextIndex + '_');
					$select1.attr('name', namePrefix + '[' + nextIndex + ']');
					$select1.attr('validate_message', "Block " + disp_i + ": " + $select1.attr('backup_validate_message'));
					$select1.find('option').first().attr('selected', 'selected');
				}
			}
		});
		return $fieldBlock1;
	}
	window.getFieldBlockNextIndex = function($fieldBlockContainer1, beginIndex, multiplier)	{
		//Increments not necessary to be 1
		multiplier = parseInt(multiplier);
		if (multiplier == 0) return 0; //multiplier can never be zero
		if (! $fieldBlockContainer1.length) return 0;
		//Get fieldBlockList 
		var $fieldBlockList1 = $fieldBlockContainer1.find('div.ui-sys-field-block');
		if (! $fieldBlockList1.length) return 0;
		//Array to store current indices 
		var indicesArray1 = [];
		$fieldBlockList1.each(function(index, val)	{
			$ele1 = $(val); //you could also say $(this)
			indicesArray1.push(parseInt($ele1.attr('data-index'))); //Extract current index
		});
		//We have array with existing indices 
		/*
		Note: Indices by default starts from 0,1,2,3,4 etc. However if the user delete element with index 2
		we expect to have something like 0,1,3,4 due so at this point next index would be 2 and not 5 
		and we will end having something like 0,1,3,4,2 . To make story worse we can have something like 9,5,6,7,2,0,4,1 which will yield a 
		next index of 3
		Procedure scan numbers from 0 to length, if not in array then that is the next number, if in array automatically the length is the next number 
		*/
		beginIndex = parseInt(beginIndex);
		var nextnumber = beginIndex + (multiplier * indicesArray1.length);
		for (var i=beginIndex; i <= (beginIndex + indicesArray1.length); i = i + multiplier)	{
			if ($.inArray(i, indicesArray1) == -1)	{
				//Not Exist in Array 
				nextnumber = i; break;
			}
		} //end-for
		return nextnumber;
	}
	window.getNextIndex = function($collection1)	{
		//Each element in collection must posses data-index attribute 
		if (! $collection1.length) return 0;
		//Array to store current indices 
		var indicesArray1 = [];
		$collection1.each(function(index, val)	{
			$ele1 = $(val);
			if ($ele1.attr('data-index'))	{
				indicesArray1.push(parseInt($ele1.attr('data-index'))); //Extract Current Index 
			}
		});
		var nextnumber = $collection1.length;
		for (var i=0; i <= indicesArray1.length; i++)	{
			if ($.inArray(i, indicesArray1) == -1)	{
				//Not Existing in Array 
				nextnumber = i; break;
			}
		}
		return nextnumber;
	}
	window.getEditListNextIndex = function(prefix)	{
		//make sure you get with both classes prefix and the ui-sys-list-edit
		var $list1 = getEditListCollections(prefix);
		if (! $list1.length) return 0;
		//Array to store current indices 
		var indicesArray1 = [];
		$list1.each(function(index, val)	{
			$ele1 = $(val); //you could also say $(this)
			indicesArray1.push(parseInt($ele1.data('index'))); //Extract current index
		});
		//We have array with existing indices 
		/*
		Note: Indices by default starts from 0,1,2,3,4 etc. However if the user delete element with index 2
		we expect to have something like 0,1,3,4 due so at this point next index would be 2 and not 5 
		and we will end having something like 0,1,3,4,2 . To make story worse we can have something like 9,5,6,7,2,0,4,1 which will yield a 
		next index of 3
		Procedure scan numbers from 0 to length, if not in array then that is the next number, if in array automatically the length is the next number 
		*/
		var nextnumber = indicesArray1.length;
		for (var i=0; i <= indicesArray1.length; i++)	{
			if ($.inArray(i, indicesArray1) == -1)	{
				//Not Exist in Array 
				nextnumber = i; break;
			}
		} //end-for
		return nextnumber;
	}
	window.removeEditFieldContainer = function(button1, containerToBeRemoved, closestParentOfCollection)	{
		/*
		button1 the source of the event 
		containerToBeRemoved , this is the container to be removed, the button1 should be a child or grandchild of the containerToBeRemoved
					most cases this is the closest enclosed div 
		closestParentOfCollection, this is a immediate parent of all container collections
		*/
		var $containerToBeRemoved1 = $(button1).closest(containerToBeRemoved);
		if (! $containerToBeRemoved1.length) return false;
		var $parent1 = $containerToBeRemoved1.closest(closestParentOfCollection);
		if (! $parent1.length) return false;
		var prefix = $parent1.data('prefix');
		/* We must atleast remain with one item */
		if (getEditListLength(prefix) == 1) return false;
		/* Proceed with removing */
		$containerToBeRemoved1.remove();
		return true;
	}
	window.addNewEditTextFieldContainer = function(button1, closestParentOfCollection)	{
		var $parent1 = $(button1).closest(closestParentOfCollection);
		if (! $parent1.length) return;
		var prefix = $parent1.data('prefix');
		var prefixClassName = prefix;
		prefixClassName = prefixClassName.replace("[", "_");
		prefixClassName = prefixClassName.replace("]", "_");
		//prefix: ie churchpastortelephone
		//imagePath: the image of the image. ie ../../sysimage/
		//closestParentOfCollection: the parent which a collection is attached to 
		//								usually is a td element of the table 
		//Get Next Index 
		//button the source of the event 
		var imagePath = $parent1.data('imagePath');
		var prefixErrorMessage = $parent1.data('messageError');
		if (! prefixErrorMessage) prefixErrorMessage = "";
		var nextIndex = getEditListNextIndex(prefix);
		//Construct a new widget 
		var expression = $parent1.attr('validate_expression');
		var message = $parent1.attr('validate_message');
		var $container1 = $('<div />').addClass(prefixClassName).addClass('ui-sys-list-edit').attr('data-index', nextIndex)
		var $input1 = $('<input/>').attr('type', 'text').attr('name', prefix + "[" + nextIndex + "]").attr('size', '32')
			.prop('required', true).attr('pattern', expression).attr('validate', 'true').attr('validate_control', 'text')
			.attr('validate_expression', expression).attr('validate_message', prefixErrorMessage + " row " + nextIndex + ": " + message);
		$input1.appendTo($container1);
		var $a1 = $('<a/>').tooltip().addClass('ui-sys-control-icon').attr('title', 'Delete: A New Added Field');
		var $img1 = $('<img/>').attr('alt', 'DAT').attr('src', imagePath + 'buttondelete.png');
		$img1.appendTo($a1);
		$a1.appendTo($container1);
		//Append the new container to the parent 
		$container1.appendTo($parent1);
		//The story has not ended , we need to shif the add button down 
		var $addButtonContainer1 = $(button1).closest('div');	//The first div of the causing event button/a 
		if (! $addButtonContainer1.length) return;
		$addButtonContainer1.appendTo($parent1);
	}
	window.addNewSelectFieldContainer = function(button1, closestParentOfCollection)	{
		var $parent1 = $(button1).closest(closestParentOfCollection);
		if (! $parent1.length) return;
		var prefix = $parent1.data('prefix');
		var prefixClassName = prefix;
		prefixClassName = prefixClassName.replace("[", "_");
		prefixClassName = prefixClassName.replace("]", "_");
		//prefix: ie churchpastortelephone
		//imagePath: the image of the image. ie ../../sysimage/
		//closestParentOfCollection: the parent which a collection is attached to 
		//								usually is a td element of the table 
		//Get Next Index 
		//button the source of the event 
		var imagePath = $parent1.data('imagePath');
		var prefixErrorMessage = $parent1.data('messageError');
		if (! prefixErrorMessage) prefixErrorMessage = "";
		var nextIndex = getEditListNextIndex(prefix);
		//Construct a new widget 
		var expression = $parent1.attr('validate_expression');
		var message = $parent1.attr('validate_message');
		var $container1 = $('<div />').addClass(prefixClassName).addClass('ui-sys-list-edit').attr('data-index', nextIndex)
		var $select1 = $('<select/>').attr('name', prefix + "[" + nextIndex + "]")
			.attr('validate', 'true').attr('validate_control', 'select')
			.attr('validate_expression', expression).attr('validate_message', prefixErrorMessage + " row " + nextIndex + ": " + message);
		//Dealing with options 
		//Copying from the existing select 
		var $referenceCollection1 = getEditListCollections(prefix).first();
		if ($referenceCollection1.length)	{
			$referenceCollection1.find('select > option').each(function(i, opt)	{
				$(opt).clone().prop('selected', false).appendTo($select1);
			});
		}//end-if
		$select1.appendTo($container1);
		var $a1 = $('<a/>').tooltip().addClass('ui-sys-control-icon').attr('title', 'Delete: A New Added Field');
		var $img1 = $('<img/>').attr('alt', 'DAT').attr('src', imagePath + 'buttondelete.png');
		$img1.appendTo($a1);
		$a1.appendTo($container1);
		//Append the new container to the parent 
		$container1.appendTo($parent1);
		//The story has not ended , we need to shif the add button down 
		var $addButtonContainer1 = $(button1).closest('div');	//The first div of the causing event button/a 
		if (! $addButtonContainer1.length) return;
		$addButtonContainer1.appendTo($parent1);
	}
	window.topLevelMenuMaintainance = function()	{
		//Checking if id label for selected item is set
		$selectedItem = $('#selected-level-one-item');
		//Removing the existing selected Menu 
		$('ul.ui-menu-level-one-wrapper li').each(function(i,v)	{
			$item = $(v);
			$item.removeClass('ui-sys-menu-level-one-selected-item');
			if ($selectedItem && ($selectedItem.val() == $item.attr('id')))	{
				$item.addClass('ui-sys-menu-level-one-selected-item');
			}
		});
	}
	$(function()	{
		//Settup buttons
		$('.button-link, input[type="button"], input[type="submit"], input[type="file"], button').button();
		$('.button-link-warning').button()
								.css({ color: 'red'});
		$('.fill-width-button-link').button()
								.css({display : 'block', width: '100%'})
		$('input[type="checkbox"]').addClass('ui-widget');
		$('div.ui-sys-accordion').accordion();
		$('div.ui-sys-tabs').tabs()
								.css({zIndex: '1'});
		//news-ticker
		$('.ui-sys-body .ui-sys-news-slide-up').easyTicker({
			direction: 'up',
			easing: 'linear'
		});
		$('.ui-sys-body .ui-sys-news-slide-down').easyTicker({
			direction: 'down',
			easing: 'linear'
		});
		$('*[title]').tooltip();
		$('.ui-sys-invoice-tooltip').tooltip({
			position: { my: "left top+15", at: "left bottom", collision: "flipfit" },
			content : function()	{
				var $content1 = $('<div/>').css({wordWrap: 'break-word', position:'relative', width: '100%'});
				var data1 = "";
				try {
					data1 = $.parseJSON($(this).find('tfoot').attr('data-invoice-popup'));
				} catch (err)	{
					return "Data parsing error " + err.message;
				}
				if (! data1) return "Could not Load Tooltip data";
				var $section1 = $('<div/>').css({position: 'relative', width: '100%', overflowX: 'hidden'});
				var $table1 = $('<table/>').addClass('pure-table').css({width: '100%', tableLayout: 'fixed', fontSize: '10px'});
				var $thead1 = $('<thead>');
				var $tr1 = $('<tr/>');
				var $td1 = $('<th/>').attr('colspan','2').text(data1.groupName);
				$td1.appendTo($tr1);
				$tr1.appendTo($thead1);
				$thead1.appendTo($table1);
				var $tbody1 = $('<tbody/>');
				$tr1 = $('<tr/>');
				$td1 = $('<td/>');
				$('<span/>').text('Gen Time').appendTo($td1);
				$td1.appendTo($tr1);
				$td1 = $('<td/>');
				$('<span/>').text(data1.generationTime).appendTo($td1);
				$td1.appendTo($tr1);
				$tr1.appendTo($tbody1);
				if (data1.feeStructures)	{
					$.each(data1.feeStructures, function(i, feeStructure1)	{
						$tr1 = $('<tr/>');
						$td1 = $('<td>');
						$('<span/>').text('Fee Structure (' + (i+1) + ')').appendTo($td1);
						$td1.appendTo($tr1);
						$td1 = $('<td/>');
						$('<span/>').text(feeStructure1.feeName).appendTo($td1);
						$('<br/>').appendTo($td1);
						$('<span/>').text(feeStructure1.currency + ' : ' + feeStructure1.amount).appendTo($td1);
						$('<br/>').appendTo($td1);
						$('<span/>').text('Valid From : ' + feeStructure1.validFrom).appendTo($td1);
						$td1.appendTo($tr1);
						$tr1.appendTo($tbody1);
					});	
				}
				
				$tbody1.appendTo($table1);
				$table1.appendTo($section1);
				$section1.appendTo($content1);
				return $content1;
			}
		});
		$('.ui-sys-login-list-tooltip').tooltip({
			position: { my: "left top+15", at: "left bottom", collision: "flipfit" },
			/*placement: function(tip, element)	{
				 var tooltipWidth = $(tip).outerWidth();
				 console.log(tooltipWidth);
			},*/
			content: function()	{
				var $content1 = $('<div/>').css({wordWrap: 'break-word', position: 'relative', width: '100%'});
				var data1 = "";
				try {
					data1 = $.parseJSON($(this).attr('data-login-list-popup'));
				} catch (err)	{
					return "Data parsing error " + err.message;
				}
				if (! data1) return "Could not Load Tooltip data";
				//Begin with Header 
				var $table1 = $('<table/>').addClass('pure-table').css({width: '100%', tableLayout: 'fixed', fontSize: '10px'});
				var $thead1 = $('<thead/>');
				var $tr1 = $('<tr/>');
				$('<th/>').attr('colspan','3').html('Owner List').css({'textAlign': 'center'}).appendTo($tr1);
				$tr1.appendTo($thead1);
				$thead1.appendTo($table1);
				var $tbody1 = $('<tbody/>');
				if (data1.owners)	{
					$.each(data1.owners, function(i, owner1)	{
						$tr1 = $('<tr/>');
						$('<td/>').html(i + 1).appendTo($tr1);
						$('<td/>').html(owner1.name).appendTo($tr1);
						var primary = "";
						if (parseInt(owner1.primary) == 1) primary = "primary";
						$('<td/>').html(primary).appendTo($tr1);
						$tr1.appendTo($tbody1);
					});
				}
				$tbody1.appendTo($table1);
				$table1.appendTo($content1);
				return $content1;
			}
		});
		$('.ui-sys-timetable-tooltip').tooltip({
			/*open: function(event, ui)	{
				this._super();
				//ui.tooltip.css({width: '800px'});
			},*/
			position: { my: "left top+15", at: "left bottom", collision: "flipfit" },
			/*placement: function(tip, element)	{
				 var tooltipWidth = $(tip).outerWidth();
				 console.log(tooltipWidth);
			},*/
			content: function()	{
				var $content1 = $('<div/>').css({wordWrap: 'break-word', position:'relative', width: '100%'});
				var data1 = "";
				try {
					data1 = $.parseJSON($(this).attr('data-timetable-popup'));
				} catch (err)	{
					return "Data parsing error " + err.message;
				}
				if (! data1) return "Could not Load Tooltip data";
				//Begin with Header 
				var $section1 = $('<div/>').css({position: 'relative', width: '100%', overflowX: 'hidden'});
				var $table1 = $('<table/>').addClass('pure-table').css({width: '100%', tableLayout: 'fixed', fontSize: '10px'});
				var $thead1 = $('<thead/>');
				var $tr1 = $('<tr/>');
				$('<th/>').attr('colspan', '2').text(data1.activityName).appendTo($tr1);
				$tr1.appendTo($thead1);
				$thead1.appendTo($table1);
				var $tbody1 = $('<tbody/>');
				if (data1.venueName && data1.venueCode)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('Venue').appendTo($tr1);
					$('<td/>').text(data1.venueName + '('+ data1.venueCode +')').appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				if (data1.dayName && data1.dayAbbreviation)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('Week Day').appendTo($tr1);
					$('<td/>').text(data1.dayName + '(' + data1.dayAbbreviation + ')').appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				if (data1.subjects)	{
					$.each(data1.subjects, function(i, subject1)	{
						$tr1 = $('<tr/>');
						$('<td/>').text('Subject_' + (i + 1)).appendTo($tr1);
						$('<td/>').text(subject1.subjectName + '(' + subject1.subjectCode + ')').appendTo($tr1);
						$tr1.appendTo($tbody1);
					});
				}
				if (data1.instructors)	{
					$.each(data1.instructors, function(i, login1)	{
						$tr1 = $('<tr/>');
						$('<td/>').text('Instructor_' + (i + 1)).appendTo($tr1);
						var $td1 = $('<td/>');
						$('<span/>').text(login1.name).appendTo($td1);
						$('<br/>').appendTo($td1);
						$('<span/>').text('Email: ' + login1.email).appendTo($td1);
						$('<br/>').appendTo($td1);
						$('<span/>').text('Phone: ' + login1.phone).appendTo($td1);
						$('<br/>').appendTo($td1);
						$td1.appendTo($tr1);
						$tr1.appendTo($tbody1);
					});
				}
				if (data1.groups)	{
					$.each(data1.groups, function(i, group1)	{
						$tr1 = $('<tr/>');
						$('<td/>').text('Group_' + (i + 1)).appendTo($tr1);
						$('<td/>').text(group1.groupName).appendTo($tr1);
						$tr1.appendTo($tbody1);
					});
				}
				if (data1.startTime)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('Start Time').appendTo($tr1);
					$('<td/>').text(data1.startTime).appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				if (data1.startTime)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('End Time').appendTo($tr1);
					$('<td/>').text(data1.endTime).appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				if (data1.validFrom)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('Valid From').appendTo($tr1);
					$('<td/>').text(data1.validFrom).appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				if (data1.validTo)	{
					$tr1 = $('<tr/>');
					$('<td/>').text('Valid To').appendTo($tr1);
					$('<td/>').text(data1.validTo).appendTo($tr1);
					$tr1.appendTo($tbody1);
				}
				$tbody1.appendTo($table1);
				$table1.appendTo($section1);
				$section1.appendTo($content1);
				//return $content1;
				return $content1;
			}
		});
		//Level One Menu Procession  --LEVEL ONE 
		topLevelMenuMaintainance();
		//END-LEVEL TWO PROCESSING
		/*BEGIN PROCESSING LIST ADD and EDIT */
		//data-error-control 
		$('div.ui-sys-list-add a').on('click', function(event)	{
			event.preventDefault();
			var $parent1 = $(this).closest('td');
			if (! $parent1.length) return;
			var $errorTarget1 = $('#' + $parent1.data('errorControl'));
			if (! $errorTarget1.length) return;
			var closestParentOfCollection = $parent1.data('closestParentOfCollection');
			$errorTarget1.empty();
			var $controlToAdd = $parent1.data('controlToAdd');
			if ($controlToAdd == "text")	{
				addNewEditTextFieldContainer(this, closestParentOfCollection);
			} else if ($controlToAdd == "select")	{
				addNewSelectFieldContainer(this, closestParentOfCollection);
			}
		});
		$('td').on('click', 'div.ui-sys-list-edit a', function(event)	{
			event.preventDefault();
			var $parent1 = $(this).closest('td');
			if (! $parent1.length) return;
			var $errorTarget1 = $('#' + $parent1.data('errorControl'));
			if (! $errorTarget1.length) return;
			var closestParentOfCollection = $parent1.data('closestParentOfCollection');
			$errorTarget1.empty();
			if (! removeEditFieldContainer(this, 'div.ui-sys-list-edit', closestParentOfCollection))	{
				$('<span/>').text('Perhaps you are trying to delete the last item in the list, at list ONE item should remain in the list')
					.appendTo($errorTarget1);
			}
		});
		/*END PROCESSING LIST ADD and EDIT*/
		/*Begin Processing Search Container*/
		$('div.ui-sys-search-container').on('click', 'a.click-to-search', function(event)	{
			event.preventDefault();
			var $button1 = $(this);
			var $text1 = $button1.closest('div.ui-sys-search-container').find('input[type="text"]');
			//Check for Anchor Tag 
			var anchorTag = "";
			if ($button1.data('anchorId'))	{
				anchorTag = "#" + $button1.data('anchorId');
			}
			//Must Exceed Minimum Character
			if (($text1.val().length >= parseInt($button1.data('minCharacter'))) || ($text1.val() == "!"))	{
				window.location.href = $button1.data('nextPage') + "&searchtext=" + escapeHtml($text1.val()) + anchorTag;
			} else	{
				$text1.attr('title', 'Your previous search was ' + $text1.val().length + ' characters long, and this search box require a minimum of ' + $button1.data('minCharacter') + ' characters');
			}
		});
		//Continuing with search results 
		var $statusTextLabel1 = $('#statustextlabel');
		var $saveResultsStorage1 = $('#saveresultsstorage');
		if ($statusTextLabel1.length && $saveResultsStorage1.length)	{
			$statusTextLabel1.text('Number of Returned Records is ' + $saveResultsStorage1.val());
			if (parseInt($saveResultsStorage1.val()) === 0)	{
				var $resultsContainer1 = $saveResultsStorage1.closest('div.ui-sys-search-results');
				$resultsContainer1.find('table').remove();
				$('<br/>').appendTo($resultsContainer1);
				$('<span/>').text('No Record(s) were found from your search, kindly try to generalize your query text').appendTo($resultsContainer1);
			}//end-if
		}//end-if
		/*End Processing Search Container*/
		/*Begin Page Redirect*/
		/*End Page Redirect*/
		$('#backRedirect').on('click', function(event)	{
			event.preventDefault();
			window.location.href = $(this).data('redirect');
		});
		/*Begin System Firewall UI All*/
		$('.ui-sys-firewall-all span input[type="checkbox"]').on('change', function(event)	{
			event.preventDefault();
			var $checkBox1 = $(this);
			var $parentBox1 = $checkBox1.closest('span');
			if (! $parentBox1.length) return;
			var $actionButton1 = $parentBox1.find('input[type="button"]');
			if (! $actionButton1.length) return;
			//Creating a new action button 
			$newActionButton1 = $('<input/>').attr('type', $actionButton1.attr('type'))
												.data('serverPath', $actionButton1.data('serverPath'))
												.data('contextCharacter', $actionButton1.data('contextCharacter'))
												.data('contextString', $actionButton1.data('contextString'))
												.data('objectType', $actionButton1.data('objectType'))
												.data('objectId', $actionButton1.data('objectId'))
												.data('contextTarget', $actionButton1.data('contextTarget'))
												.data('nextPage', $actionButton1.data('nextPage'))
												.data('secureCode', $actionButton1.data('secureCode'))
												.val($actionButton1.val())
												.attr('title', $actionButton1.attr('title'))
												.prop('disabled', !$checkBox1.prop('checked'))
												.tooltip()
												.button();
												
			$actionButton1.remove();
			if ($checkBox1.prop('checked'))	{
				$newActionButton1.on('click', function()	{
					event.preventDefault();
					systemFirewallSaveAll(this);
				});
			}//end-if
			//Now we are moving well
			//Append the new created actionButton 
			$newActionButton1.appendTo($parentBox1);
		});
		//Now capturing value for rows when data change, all value 
		//will be saved on a row data-context-character variable 
		$('table.ui-sys-context-table tr td select.ui-sys-context-allow-select').on('change', function(event)	{
			event.preventDefault();
			var $select1 = $(this).closest('select'); /* Avoid pointing to options */
			if (! $select1.length) return;
			var $containingRow1 = $select1.closest('tr');
			if (! $containingRow1.length) return;
			$containingRow1.data('contextCharacter', $select1.val());
		});
		//Let us go to buttons 
		$('table.ui-sys-context-table tr td input.ui-sys-control-radio').on('change', function(event)	{
			event.preventDefault();
			var $radioButton1 = $(this);
			//Get Select Control residing in the same row 
			var $containingRow1 = $radioButton1.closest('tr');
			if (! $containingRow1.length) return;
			var $select1 = $containingRow1.find('select.ui-sys-context-allow-select');
			if (! $select1.length) return;
			if ($radioButton1.hasClass('control-radio-allow'))	{
				$select1.prop('disabled', false);
			} else {
				$select1.prop('disabled', true);
			}//end-if-else
			var characterToSave = $radioButton1.val();
			if (! $select1.prop('disabled'))	{
				//Allow save this level 
				characterToSave = $select1.val();
			}
			//Save this in a row 
			$containingRow1.data('contextCharacter', characterToSave);
		});
		//Saving Button 
		$('#firewallRulesSavingCommand').on('click', function(event)	{
			event.preventDefault();
			systemFirewallSaveCustom(this);
		});
		/*End System Firewall UI All*/
		/*Beginning Enable/Disable Input Panel*/
		$('input.field-checkbox').on('change', function(event)	{
			event.preventDefault();
			var $check1 = $(this);
			var $parent1 = $check1.closest('tr.field-container');
			$parent1.find('.field-capturedata').prop('disabled', !$check1.prop('checked'));
			//Dealing with list input fields 
			$parent1.find('.ui-sys-list-parent .ui-sys-list-edit').each(function(index, ele1)	{
				var $ele1 = $(ele1);
				$ele1.find('input').prop('disabled', !$check1.prop('checked'));
				$ele1.find('select').prop('disabled', !$check1.prop('checked'));
				var $deleteButton = $ele1.find('a.ui-sys-control-icon');
				if ($check1.prop('checked'))	{
					$deleteButton.removeClass('ui-sys-hidden');
				} else {
					$deleteButton.addClass('ui-sys-hidden');
				}
			});
			//Dealing with Add Control 
			var $addButton = $parent1.find('.ui-sys-list-parent .ui-sys-list-add a.ui-sys-control-icon');
			if ($check1.prop('checked'))	{
				$addButton.removeClass('ui-sys-hidden');
			} else {
				$addButton.addClass('ui-sys-hidden');
			}
		});
		/*End Enable/Disable Input Panel*/
		//Dealing with Sortable 
		$('.ui-sortable-button').on('click', function(event)	{
			event.preventDefault();
			var $selectedRow1 = $(this).closest('tr.ui-sortable-row');
			if (! $selectedRow1.length) return;
			var $tableContainer1 = $(this).closest('table.ui-sortable-table');
			if (! $tableContainer1.length) return;
			$tableContainer1.find('tr.ui-sortable-row').each(function(i, row)	{
				var $row1 = $(row);
				if ($row1.hasClass('ui-sys-sortable-selected')) $row1.removeClass('ui-sys-sortable-selected');
			});
			//Add to this row now 
			$selectedRow1.addClass('ui-sys-sortable-selected');
			//We need to update UI 
			uiSortableContainerMaintainance('div.ui-sortable-container');
		});
		//Clear Button 
		$('div.ui-sortable-container a.ui-sys-clear').on('click', function(event)	{
			event.preventDefault();
			var $container1 = $(this).closest('div.ui-sortable-container');
			if (! $container1.length) return;
			$container1.find('table.ui-sortable-table tr.ui-sortable-row').each(function(i, row)	{
				var $row1 = $(row);
				if ($row1.hasClass('ui-sys-sortable-selected')) $row1.removeClass('ui-sys-sortable-selected');
			});
			//We need to update UI 
			uiSortableContainerMaintainance('div.ui-sortable-container');
		});
		//Up Button 
		$('div.ui-sortable-container a.ui-sys-move-up').on('click', function(event)	{
			event.preventDefault();
			var $container1 = $(this).closest('div.ui-sortable-container');
			if (! $container1.length) return;
			var $selectedRow1 = $container1.find('table.ui-sortable-table tr.ui-sys-sortable-selected');
			if ($selectedRow1.length)	{
				//Row Found 
				var $previousRow1 = $selectedRow1.prev();
				if ($previousRow1.length)	{
					$selectedRow1.insertBefore($previousRow1);
				}
			}
			//We need to update UI 
			uiSortableContainerMaintainance('div.ui-sortable-container');
			uiSortableContainerDataMaintainaceAfterMoveUpDown('div.ui-sortable-container');
		});
		//Down Button 
		$('div.ui-sortable-container a.ui-sys-move-down').on('click', function(event)	{
			event.preventDefault();
			var $container1 = $(this).closest('div.ui-sortable-container');
			if (! $container1.length) return;
			var $selectedRow1 = $container1.find('table.ui-sortable-table tr.ui-sys-sortable-selected');
			if ($selectedRow1.length)	{
				//Row Found 
				var $nextRow1 = $selectedRow1.next();
				if ($nextRow1.length)	{
					$selectedRow1.insertAfter($nextRow1);
				}
			}
			//We need to update UI 
			uiSortableContainerMaintainance('div.ui-sortable-container');
			uiSortableContainerDataMaintainaceAfterMoveUpDown('div.ui-sortable-container');
		});
		/*Beginning field-block-operations*/
		$('div.ui-sys-field-block-add a').on('click', function(event)	{
			var $actionButton1 = $(this);
			event.preventDefault();
			//Check if not disabled 
			if ($actionButton1.hasClass('disable-action-button')) return;
			var $container1 = $actionButton1.closest('div.ui-sys-field-block-container');
			if (! $container1.length) return;
			//There must be an error throwing element with a perror id 
			var $target1 = $('#perror');
			if (! $target1.length) return;
			$target1.empty();
			var nextIndex = window.getFieldBlockNextIndex($container1, 0, 1);
			var $referenceFieldBlock1 = $container1.find('div.ui-sys-field-block').first();
			if (! $referenceFieldBlock1.length) return;
			var $newFieldBlock1 = $referenceFieldBlock1.clone(true);
			//Some modification to be done 
			$newFieldBlock1 = window.updateFieldBlock($newFieldBlock1, nextIndex);
			$newFieldBlock1.appendTo($container1);
			//We need to reposition the Add control 
			$container1.find('div.ui-sys-field-block-add').appendTo($container1);
		});
		$('div.ui-sys-field-block-remove a').on('click', function(event)	{
			event.preventDefault();
			var $actionButton1  = $(this);
			//Check if disabled 
			if ($actionButton1.hasClass('disable-action-button')) return;
			//Block To Remove 
			var $fieldBlock1 = $actionButton1.closest('div.ui-sys-field-block');
			if (! $fieldBlock1.length) return;
			//Container now
			var $container1 = $fieldBlock1.closest('div.ui-sys-field-block-container');
			if (! $container1.length) return;
			
			//There must be an error throwing element with a perror id 
			var $target1 = $('#perror');
			if (! $target1.length) return;
			$target1.empty();
			//Make sure we leave atleast one fieldBlock 
			var $fieldBlockList1 = $container1.find('div.ui-sys-field-block');
			if (! $fieldBlockList1.length) return;
			if ($fieldBlockList1.length == 1)	{
				$target1.html("Atleast One Block Should remain");
				return;
			}
			//Actual Remove
			$fieldBlock1.remove();
		});
		/*Ending field-block-operations*/
		/*Beginning Dealing With Block Operation caring it is working with CSV Framework*/
		$('div.ui-sys-field-block-add-csv a').on('click', function(event)	{
			var $actionButton1 = $(this);
			event.preventDefault();
			//Check if not disabled 
			if ($actionButton1.hasClass('disable-action-button')) return;
			var $container1 = $actionButton1.closest('div.ui-sys-field-block-container-csv');
			if (! $container1.length) return;
			//There must be an error throwing element with a perror id 
			var $target1 = $('#perror');
			if (! $target1.length) return;
			$target1.empty();
			var baseIndex = $container1.attr('data-base-index');
			if (! baseIndex) return;
			//Multiple of 64
			var nextIndex = window.getFieldBlockNextIndex($container1, baseIndex, 64);
			var $referenceFieldBlock1 = $container1.find('div.ui-sys-field-block').first();
			if (! $referenceFieldBlock1.length) return;
			var $newFieldBlock1 = $referenceFieldBlock1.clone(true);
			//Some modification to be done 
			$newFieldBlock1 = window.updateFieldBlockCSV($newFieldBlock1, nextIndex); 
			$newFieldBlock1.appendTo($container1);
			//We need to reposition the Add control 
			$container1.find('div.ui-sys-field-block-add-csv').appendTo($container1);
		});
		$('div.ui-sys-field-block-remove-csv a').on('click', function(event)	{
			event.preventDefault();
			var $actionButton1  = $(this);
			//Check if disabled 
			if ($actionButton1.hasClass('disable-action-button')) return;
			//Block To Remove 
			var $fieldBlock1 = $actionButton1.closest('div.ui-sys-field-block');
			if (! $fieldBlock1.length) return;
			//Container now
			var $container1 = $fieldBlock1.closest('div.ui-sys-field-block-container-csv');
			if (! $container1.length) return;
			
			//There must be an error throwing element with a perror id 
			var $target1 = $('#perror');
			if (! $target1.length) return;
			$target1.empty();
			//Make sure we leave atleast one fieldBlock 
			var $fieldBlockList1 = $container1.find('div.ui-sys-field-block');
			if (! $fieldBlockList1.length) return;
			if ($fieldBlockList1.length == 1)	{
				$target1.html("Atleast One Block Should remain");
				return;
			}
			//Actual Remove
			$fieldBlock1.remove();
		});
		/*Ending Dealing With Block Operation caring it is working with CSV Framework*/
		//Printing 
		$('div.ui-sys-printable-controls a').on('click', function(event)	{
			event.preventDefault();
			var $button1 = $(this).closest('a');
			if (! $button1.length) return;
			var $printArea1 = $button1.closest('div.ui-sys-printable');
			$button1.hide();
			if (! $printArea1.length) {
				$button1.show();
				return;
			}
			$printArea1.printArea(); //Make sure the PrintArea plugin is installed
			$button1.show();
		});
		$('a.menu-icon-print').on('click', function(event)	{
			event.preventDefault();
			var $button1 = $(this).closest('a'); //Make sure it is an anchor tag and not any inner element 
			if (! $button1.length) return;
			var printAreaIdentification = $button1.attr('data-content-container');
			if (! printAreaIdentification) return;
			var $printArea1 = $('#' + printAreaIdentification);
			if (! $printArea1.length) return;
			$printArea1.printArea();	//Make sure the PrintArea plugin is installed 
		});
		//Logout control
		$('.ui-sys-logout-control').on('click', function(event)	{
			event.preventDefault();
			var $logoutButton1 = $(this).closest('a');
			if (! $logoutButton1.length) return;
			var targetElement = $logoutButton1.attr('data-error-control');
			if (! targetElement) return;
			//Validation Should begun
			//Common if bypasses 
			$target1 = $('<div/>');
			if (! $logoutButton1.hasClass('bypass-data-error-control'))	{
				$target1 = $('#' + targetElement);
				if (! $target1.length) return;
				$target1.empty();
			}
			//Ajax should do its job here
			//You can load gif image 
			var nextPage = $logoutButton1.attr('data-next-page');
			if (! nextPage) return;		
			var serverDirectory = $logoutButton1.attr('data-server-directory');
			if (! serverDirectory) return;
			serverDirectory = serverDirectory + "/service_no_authenticate.php";
			$.ajax({
				url: serverDirectory,
				method: "POST",
				data: { param1: "zoomtong@2016" },
				dataType: "json",
				cache: false,
				async: false
			}).done(function(data, textStatus, jqXHR)	{
				if (parseInt(data.code) === 0)	{
					//Successful 
					window.location.href = nextPage;
				} else	{
					//Failed 
					$('<span/>').text(data.message)
						.appendTo($target1);
					return;
				}
			}).fail(function(jqXHR, textStatus, errorThrown)	{
				$('<span/>').text(textStatus)
					.appendTo($target1);
			}).always(function(data, textStatus, jqXHR)	{
				console.log("System Logout");
			});
		});
		/*Begin Dialog for CSV Preview*/
		$('#__id_csv_previewer').on('click', function(event)	{
			event.preventDefault();
			var $button1 = $(this).closest('a');
			if (! $button1.length) return;
			var $container1 = $('#' + $button1.attr('data-dialog-container'));
			if (! $container1.length) return;
			$container1.empty();
			var filename = $button1.attr('data-file-to-read');
			if (! filename) return;
			var serverpath = $button1.attr('data-server-forward-path');
			if (! serverpath) return;
			var $target1 = $('#' + $button1.attr('data-error-control'));
			if (! $target1.length) return;
			$target1.empty();
			$.ajax({
				url: serverpath,
				method: "POST",
				data: { param1: filename },
				dataType: "json",
				cache: false,
				async: false
			}).done(function(data, textStatus, jqXHR)	{
				if (parseInt(data.code) === 0)	{
					var width = $(window).width();
					var height = $(window).height();
					var containerWidth = 0.8 * width;
					var containerHeight = 0.9 * height;
					var $panelContainer1 = $('<div>').addClass('ui-sys-panel-container')
							.addClass('ui-widget').addClass('ui-widget-content')
							.css({position: 'absolute',
									top: '5px',
									left:((width - containerWidth) / 2) + 'px',
									padding: '1px',
									border: '2px solid gold',
									zIndex: '10',
									width: containerWidth + 'px',
									backgroundColor: 'white'});
					var $panelHeader1 = $('<div/>').addClass('ui-sys-panel-header').addClass('ui-widget-header');
					$('<span/>').html("CSV Previewer").appendTo($panelHeader1);
					$('<a/>').addClass('button-link').css({float: 'right', cursor: 'pointer'})
							.html('Close Preview')
							.on('click', function(event)	{
								event.preventDefault();
								$container1.empty();
							}).appendTo($panelHeader1);
					$panelHeader1.appendTo($panelContainer1);
					var $panelContent1 = $('<div/>').addClass('ui-sys-panel-body')
						.css({clear: 'both',
							overflowX: 'scroll',
							overflowY: 'scroll',
							maxHeight: '100%',
							background: 'white'});
					var recordsLimitPerPage = data.recordsLimitPerPage;
					var $table1 = $('<table/>').addClass('pure-table').addClass('pure-table-bordered').addClass('ui-sys-table-search-results');
					var rowCount = 0;
					var $tbody1 = $('<tbody/>'); //Just Incase
					var numberOfTBodies = 0;
					for (var i in data.rows)	{
						var tr1 = data.rows[i].tr;
						var $tr1 = $('<tr/>');
						if (rowCount == 0)	{
							//Header Only Now 
							var $thead1 = $('<thead/>');
							$('<th/>').appendTo($tr1); //for S/N
							for (var j in tr1)	{
								var th1 = tr1[j].td;
								$('<th/>').html(th1).appendTo($tr1);
							}
							$tr1.appendTo($thead1);
							$thead1.appendTo($table1);
						} else {
							if (((rowCount - 1) % recordsLimitPerPage) == 0)	{
								$tbody1 = $('<tbody/>');
								if (numberOfTBodies != 0)	{
									$tbody1.addClass('ui-sys-hidden');
								}
								$tbody1.appendTo($table1);
								numberOfTBodies++;
							}
							//Continue Appending To tbody 
							$('<td/>').html(rowCount).appendTo($tr1);
							for (var j in tr1)	{
								var td1 = tr1[j].td;
								$('<td/>').html(td1).appendTo($tr1);
							}
							$tr1.appendTo($tbody1);
						}
						rowCount++;
					}
					$table1.appendTo($panelContent1);
					$panelContent1.appendTo($panelContainer1);
					var $panelFooter1 = $('<div/>').addClass('ui-sys-panel-footer');
					var $ul1 = $('<ul/>');
					$ul1.twbsPagination({
						totalPages: numberOfTBodies,
						visiblePages: 5,
						onPageClick: function (event, page) {
							//page is page number 
							var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
							if (! $tbodyList1.length) return;
							//Hide All tbody 
							$tbodyList1.addClass('ui-sys-hidden');
							//Now show only the one corresponding to this page
							$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
						}
					});
					$ul1.appendTo($panelFooter1);
					$panelFooter1.appendTo($panelContainer1);
					$panelContainer1.appendTo($container1);
				} else	{
					//Failed 
					$('<span/>').text(data.message)
						.appendTo($target1);
					return;
				}
			}).fail(function(jqXHR, textStatus, errorThrown)	{
				$('<span/>').text(textStatus)
					.appendTo($target1);
			}).always(function(data, textStatus, jqXHR)	{
				console.log("CSV File Loaded");
			});
		});
		/*End Dialog for CSV Preview*/
		//Uploaded Image 
		/* $('input.ui-sys-file-upload').on('change', function(event)	{
			$(this).data('trackchange', '1');
			showImageToBeUploaded(this, '.photodisplay img');
		});*/
		/*Begin Pickup Processor*/
		$('ol.pickup-data a.selectable-item').on('click', function(event)	{
			var $a1 = $(this).closest('a.selectable-item');
			if (! $a1.length) return;
			var $li1 = $a1.closest('li');
			if (! $li1.length) return;
			if ($a1.hasClass('not-selected'))	{
				$li1.appendTo($('div.ui-sys-pickup-pickedlist ol.pickup-data'));
				$a1.removeClass('not-selected').addClass('is-selected');
			} else if ($a1.hasClass('is-selected'))	{
				$li1.appendTo($('div.ui-sys-pickup-pickuplist ol.pickup-data'));
				$a1.removeClass('is-selected').addClass('not-selected')
			}
		});
		/*End Pickup Processor*/
		/*Begin of Messaging Processor*/
		$('div.ui-sys-message .checkbox1').on('change', function(event)	{
			var $container1 = $(this).closest('div.ui-sys-message');
			if (! $container1.length) return;
			var $button1 = $container1.find('div.ui-sys-message-footer .__a_load_panel_data');
			if (! $button1.length) return;
			var $buttonParent1 = $button1.closest('div.ui-sys-message-footer');
			if (! $buttonParent1.length) return;
			var checkbox1 = this;
			var $tempButton1 = $button1;
			$button1 = $('<input>').attr('type', 'button')
				.attr('value', $tempButton1.val())
				.attr('data-dialog-container', $tempButton1.attr('data-dialog-container'))
				.attr('data-server-forward-path', $tempButton1.attr('data-server-forward-path'))
				.attr('data-error-control', $tempButton1.attr('data-error-control'))
				.attr('data-name-prefix', $tempButton1.attr('data-name-prefix'))
				.attr('data-message-type', $tempButton1.attr('data-message-type'))
				.attr('id', $tempButton1.attr('id'))
				.on('click', loadDataGeneral)
				.addClass('__a_load_panel_data');
			if ($tempButton1.hasClass('ui-sys-hidden')) $button1.addClass('ui-sys-hidden'); //Always hide
			$tempButton1.remove();
			if (checkbox1.checked)	{
				//Enable all control 
				$container1.find('input').prop('disabled', false);
				$container1.find('select').prop('disabled', false);
				$button1.button()
					.appendTo($buttonParent1);
			} else	{
				//Disable All Controls 
				$container1.find('input').prop('disabled', true);
				$container1.find('select').prop('disabled', true);
				$button1.prop('disabled', true)
					.button()
					.appendTo($buttonParent1);
			}
			checkbox1.disabled = false;
		});
		/*Begin Loading Data*/
		function loadDataGeneral(event)	{
			var $button1 = $(this);
			if (! $button1.length) return;
			var $dataContainer1 = $button1.closest('div.ui-sys-panel-data-container');
			if (! $dataContainer1.length) return;
			var $container1 = $('#' + $button1.attr('data-dialog-container'));
			if (! $container1.length) return;
			$container1.empty();
			var serverpath = $button1.attr('data-server-forward-path');
			if (! serverpath) return;
			var $target1 = $('#' + $button1.attr('data-error-control'));
			if (! $target1.length) return;
			var prefix = $button1.attr('data-name-prefix');
			if (! prefix) return;
			$target1.empty();
			$.ajax({
				url: serverpath,
				method: "POST",
				data: { param1: prefix, param2: $button1.attr('data-message-type') },
				dataType: "json",
				cache: false,
				async: false
			}).done(function(data, textStatus, jqXHR)	{
				//window.alert(JSON.stringify(data));
				if (parseInt(data.code) === 0)	{
					var prefix = data.prefix;
					var width = $(window).width();
					var height = $(window).height();
					var containerWidth = 0.8 * width;
					var containerHeight = 0.9 * height;
					var $panelContainer1 = $('<div>').addClass('ui-sys-panel-container')
							.addClass('ui-widget').addClass('ui-widget-content')
							.css({position: 'absolute',
									top: '5px',
									left: ((width - containerWidth) / 2) + 'px',
									padding: '1px',
									border: '2px solid gold',
									zIndex: '800',
									width: containerWidth + 'px',
									backgroundColor: 'white'});
					var $panelHeader1 = $('<div/>').addClass('ui-sys-panel-header').addClass('ui-widget-header');
					$('<span/>').html("Data Panel").appendTo($panelHeader1);
					$('<a/>').addClass('button-link').css({float: 'right', cursor: 'pointer'})
							.html('Close Preview')
							.on('click', function(event)	{
								event.preventDefault();
								$container1.empty();
							}).appendTo($panelHeader1);
					$panelHeader1.appendTo($panelContainer1);
					var $panelContent1 = $('<div/>').addClass('ui-sys-panel-body')
						.css({clear: 'both',
							overflowX: 'scroll',
							overflowY: 'scroll',
							maxHeight: '100%',
							background: 'white'});
					var recordsLimitPerPage = data.recordsLimitPerPage;
					var $table1 = $('<table/>').addClass('pure-table').addClass('pure-table-bordered').addClass('ui-sys-table-search-results').css({ zIndex: '100' });
					var rowCount = 0;
					var $tbody1 = $('<tbody/>'); //Just Incase
					var numberOfTBodies = 0;
					for (var i in data.rows)	{
						var tr1 = data.rows[i].tr;
						var __id = data.rows[i].id;
						var $tr1 = $('<tr/>');
						if (rowCount == 0)	{
							//Header Only Now 
							var $thead1 = $('<thead/>');
							$('<th/>').appendTo($tr1); //for checkbox1
							$('<th/>').appendTo($tr1); //for S/N
							for (var j in tr1)	{
								var th1 = tr1[j].td;
								$('<th/>').html(th1).appendTo($tr1);
							}
							$tr1.appendTo($thead1);
							$thead1.appendTo($table1);
						} else {
							if (((rowCount - 1) % recordsLimitPerPage) == 0)	{
								$tbody1 = $('<tbody/>'); 
								if (numberOfTBodies != 0)	{
									$tbody1.addClass('ui-sys-hidden');
								}
								$tbody1.appendTo($table1);
								numberOfTBodies++;
							}
							//Continue Appending To tbody 
							var $td1 = $('<td/>');
							$('<input/>').attr('type', 'checkbox')
								.attr('name', prefix + '[' + (rowCount - 1) + ']')
								.attr('value', __id)
								.addClass('__98_checkbox')
								.appendTo($td1)
								.css({
									zIndex: '100'
								});
							$td1.appendTo($tr1);
							$('<td/>').html(rowCount).appendTo($tr1);
							for (var j in tr1)	{
								var td1 = tr1[j].td;
								if (td1 == "_@32767@_") td1 = "";
								$('<td/>').html(td1).appendTo($tr1);
							}
							$tr1.appendTo($tbody1);
						}
						rowCount++;
					}
					$table1.appendTo($panelContent1);
					$panelContent1.appendTo($panelContainer1);
					var $panelFooter1 = $('<div/>').addClass('ui-sys-panel-footer');
					var $ul1 = $('<ul/>');
					$ul1.twbsPagination({
						totalPages: numberOfTBodies,
						visiblePages: 5,
						onPageClick: function (event, page) {
							//page is page number 
							var $tbodyList1 = $('table.ui-sys-table-search-results tbody');
							if (! $tbodyList1.length) return;
							//Hide All tbody 
							$tbodyList1.addClass('ui-sys-hidden');
							//Now show only the one corresponding to this page
							$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
						}
					});
					$ul1.appendTo($panelFooter1);
					var $div1 = $('<div/>')
						.addClass('ui-sys-inline-controls-right')
						.css({padding: '5px', margin: '5px;'});
					$('<input/>')
						.attr('type', 'button')
						.attr('value', 'Append Selected')
						.addClass('ui-sys-inline-controls-right')
						.button()
						.on('click', function(event)	{
							var $targetTable1 = $dataContainer1.find('table.ui-sys-data-table');
							if (! $targetTable1.length) return;
							var $targetTbody1 = $targetTable1.find('tbody');
							if (! $targetTbody1.length) return;
							/*We need to append All selected rows */
							//Source List Now 
							$table1.find('tbody tr').each(function(i, row)	{
								var $row1 = $(row);
								var $checkbox1 = $row1.find('input.__98_checkbox');
								if ($checkbox1.length && $checkbox1.prop('checked'))	{
									console.log("Found index " + i);
									//We should check append if not exists at the target 
									var isExisting = false;
									$targetTbody1.find('tr').each(function(j, trow)	{
										var $trow1 =$(trow);
										var $tcheckbox1 = $trow1.find('input.__98_checkbox');
										if ($tcheckbox1.length)	{
											if ($checkbox1.attr('name') == $tcheckbox1.attr('name'))	{
												isExisting = true;
											}
										}
									});
									if (! isExisting) $row1.appendTo($targetTbody1);
								}
							});	
							$container1.empty();
						})
						.appendTo($div1);
					$div1.appendTo($panelFooter1);
					$panelFooter1.appendTo($panelContainer1);
					$panelContainer1.appendTo($container1);
				} else	{
					//Failed 
					$('<span/>').text(data.message)
						.appendTo($target1);
					return;
				}
			}).fail(function(jqXHR, textStatus, errorThrown)	{
				$('<span/>').text(textStatus)
					.appendTo($target1);
			}).always(function(data, textStatus, jqXHR)	{
				console.log("CSV File Loaded");
			});
		}
		$('.__a_load_panel_data').on('click', loadDataGeneral);
		/*End Loading Data*/
		/*End of Messaging Processor*/
		/*BEGIN: Approval Sequence Schmema/Data*/
		$('div.ui-sys-approve-action input.button-intermediate-approve-reject-action').on('click', function(event)	{
			var $button1 = $(this);
			var dataId = $button1.attr('data-data-id');
			if (! dataId) return;
			var serverPath = $button1.attr('data-server-path');
			if (! serverPath) return;
			var schemaName = $button1.attr('data-schema-name');
			if (! schemaName) return;
			var requestedBy = $button1.attr('data-requested-by');
			if (! requestedBy) return;
			var timeString = $button1.attr('data-time');
			if (! timeString) return;
			var $parent1 = $button1.closest('div.ui-sys-approve-action');
			if (! $parent1.length) return;
			$parent1.empty(); //Wipe Content 
			var $container1 = $('<div/>').addClass('ui-sys-panel-container')
				.addClass('ui-sys-panel')
				.addClass('ui-widget')
				.addClass('ui-widget-content');
			var $table1 = $('<table/>').addClass('pure-table').addClass('pure-table-horizontal').css({width: '100%'});
			var $thead1 = $('<thead/>');
			var $tr1 = $('<tr/>');
			var $th1 = $('<th>').addClass('ui-sys-inline-controls-center').html(schemaName);
			$th1.appendTo($tr1);
			$tr1.appendTo($thead1);
			$thead1.appendTo($table1);
			var $tbody1 = $('<tbody>');
			$tr1 = $('<tr/>');
			var $td1 = $('<td/>');
			var $div1 = $('<div/>').addClass('ui-sys-warning');
			$('<span/>').html('You are about to ' + schemaName).appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('Target person who will be affected is ').appendTo($div1);
			$('<b/>').html(requestedBy).appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('Are You sure you want to proceed?').appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('(If you are not sure, kindly click on the HOME button, just below your profile photo)').appendTo($div1);
			$div1.appendTo($td1);
			$td1.appendTo($tr1);
			$tr1.appendTo($tbody1);
			$tr1 = $('<tr/>');
			$td1 = $('<td>').addClass('ui-sys-inline-controls-right');
			$controlButton1 = $('<input/>').attr('type', 'button')
				.attr('data-server-path', serverPath)
				.attr('data-data-id', dataId)
				.attr('title', 'This will dismiss and stop this process')
				.attr('data-time', timeString)
				.val('Confirm Reject')
				.addClass('button-approval-approve-action')
				.tooltip()
				.button();
			$controlButton1.appendTo($td1);
			$td1.appendTo($tr1);
			$tr1.appendTo($tbody1);
			$tbody1.appendTo($table1);
			$table1.appendTo($container1);
			$container1.appendTo($parent1);
		});
		$('div.ui-sys-approve-action input.button-intermediate-approve-action').on('click', function(event)	{
			var $button1 = $(this);
			var dataId = $button1.attr('data-data-id');
			if (! dataId) return;
			var serverPath = $button1.attr('data-server-path');
			if (! serverPath) return;
			var schemaName = $button1.attr('data-schema-name');
			if (! schemaName) return;
			var requestedBy = $button1.attr('data-requested-by');
			if (! requestedBy) return;
			var timeString = $button1.attr('data-time');
			if (! timeString) return;
			var $parent1 = $button1.closest('div.ui-sys-approve-action');
			if (! $parent1.length) return;
			$parent1.empty(); //Wipe Content 
			var $container1 = $('<div/>').addClass('ui-sys-panel-container')
				.addClass('ui-sys-panel')
				.addClass('ui-widget')
				.addClass('ui-widget-content');
			var $table1 = $('<table/>').addClass('pure-table').addClass('pure-table-horizontal').css({width: '100%'});
			var $thead1 = $('<thead/>');
			var $tr1 = $('<tr/>');
			var $th1 = $('<th>').addClass('ui-sys-inline-controls-center').html(schemaName);
			$th1.appendTo($tr1);
			$tr1.appendTo($thead1);
			$thead1.appendTo($table1);
			var $tbody1 = $('<tbody>');
			$tr1 = $('<tr/>');
			var $td1 = $('<td/>');
			var $div1 = $('<div/>').addClass('ui-sys-warning');
			$('<span/>').html('You are about to ' + schemaName).appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('Target person who will be affected is ').appendTo($div1);
			$('<b/>').html(requestedBy).appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('Are You sure you want to proceed?').appendTo($div1);
			$('<br/>').appendTo($div1);
			$('<span/>').html('(If you are not sure, kindly click on the HOME button, just below your profile photo)').appendTo($div1);
			$div1.appendTo($td1);
			$td1.appendTo($tr1);
			$tr1.appendTo($tbody1);
			$tr1 = $('<tr/>');
			$td1 = $('<td>').addClass('ui-sys-inline-controls-right');
			$controlButton1 = $('<input/>').attr('type', 'button')
				.attr('data-server-path', serverPath)
				.attr('data-data-id', dataId)
				.attr('data-time', timeString)
				.val('Confirm Approval')
				.addClass('button-approval-approve-action')
				.button();
			$controlButton1.appendTo($td1);
			$td1.appendTo($tr1);
			$tr1.appendTo($tbody1);
			$tbody1.appendTo($table1);
			$table1.appendTo($container1);
			$container1.appendTo($parent1);
		});
		$('div.ui-sys-approve-action').on('click', 'input.button-approval-approve-action', function(event)	{
			var $button1 = $(this);
			var dataId = $button1.attr('data-data-id');
			if (! dataId) return;
			var serverPath = $button1.attr('data-server-path');
			if (! serverPath) return;
			var timeString = $button1.attr('data-time');
			if (! timeString) return;
			var $parent1 = $button1.closest('div.ui-sys-approve-action');
			if (! $parent1.length) return;
			//Proceed to Ajax Execution
			$.ajax({
				url: serverPath,
				method: "POST",
				data: { param1: dataId, param2: timeString },
				dataType: "json",
				cache: false,
				async: false
			}).done(function(data, textStatus, jqXHR)	{
				if (parseInt(data.code) === 0)	{
					//Successful
					$parent1.empty();
					var $container1 = $('<div>')
						.addClass('ui-sys-panel-container')
						.addClass('ui-sys-panel')
						.addClass('ui-widget')
						.addClass('ui-widget-content');
					$('<div/>').addClass('ui-widget-header')
						.html('Approval Report')
						.appendTo($container1);
					$('<div/>').addClass('ui-sys-highlight')
						.html(data.message).appendTo($container1);
					var $div1 = $('<div/>').addClass('ui-sys-inline-controls-right');
					$('<input/>').attr('type', 'button')
						.val('Close')
						.on('click', function(event)	{
							$parent1.remove();
						})
						.button()
						.appendTo($div1);
					$div1.appendTo($container1);
					$container1.appendTo($parent1);
				} else	{
					//Failed
					$('<span/>').text(data.message).appendTo($errorTarget1);
				}
			}).fail(function(jqXHR, textStatus, errorThrown)	{
				$('<span/>').text(textStatus).appendTo($errorTarget1);
			}).always(function(data, textStatus, jqXHR)	{
			
			});
		});
		$('div.ui-sys-approval-window input.button-approval-submit-new').on('click', function(event)	{
			if (! generalFormValidation(this, 'approval-form-1', 'ui-sys-error-message'))	{
				return;
			}
			var $button1 = $(this);
			var $errorTarget1  = $('#ui-sys-error-message');
			if ( ! $errorTarget1.length) return;
			$errorTarget1.empty();
			var $parent1 = $button1.closest('div.ui-sys-approval-window');
			if (! $parent1) return;
			var $textArea1 = $parent1.find('textarea.text-approval-data');
			if (! $textArea1.length) return;
			var serverPath = $button1.attr('data-server-path');
			if (! serverPath) return;
			var schemaId = $button1.attr('data-schema-id');
			if (! schemaId) return;
			var loginId = $button1.attr('data-login-id');
			if (! loginId) return;
			var specialInstruction = $button1.attr('data-special-instruction');
			if (! specialInstruction) return;
			//Proceed to Ajax Execution
			$.ajax({
				url: serverPath,
				method: "POST",
				data: { param1: schemaId, param2: $textArea1.val(), param3: loginId, param4: specialInstruction },
				dataType: "json",
				cache: false,
				async: false
			}).done(function(data, textStatus, jqXHR)	{
				if (parseInt(data.code) === 0)	{
					//Successful 
					$parent1.empty();
					$('<div/>').addClass('ui-sys-panel-header').addClass('ui-widget-header').html('Data Approval Initialized')
						.appendTo($parent1);
					$('<div/>').addClass('ui-sys-panel-body').addClass('ui-sys-data-capture').addClass('ui-state-highlight')
						.html('Change of Data Approval has been Initialized Successful. Once all Approving Parties have approved you can proceed on Changing the requested Data. NOTE: Changing of this data is one time event and ONCE ONLY')
						.appendTo($parent1);
					var $footer1 = $('<div/>').addClass('ui-sys-panel-footer');
					$('<input/>').attr('type','button').attr('value', 'Close').on('click', function(event)	{
						$parent1.remove();
					}).button().appendTo($footer1);
					$footer1.appendTo($parent1);
		
				} else	{
					//Failed
					$('<span/>').text(data.message).appendTo($errorTarget1);
				}
			}).fail(function(jqXHR, textStatus, errorThrown)	{
				$('<span/>').text(textStatus).appendTo($errorTarget1);
			}).always(function(data, textStatus, jqXHR)	{
			
			});
		});
		$('div.ui-sys-approval-window input.close-approval-window').on('click', function(event)	{
			var $parent1 = $(this).closest('div.ui-sys-approval-window');
			if (! $parent1.length) return;
			$parent1.remove();
		});
		/*END: Approval Sequence Schmema/Data*/
	});
})(jQuery);
var Parser	= {
	parseXml: function(xml) {
		var dom = null;
		if (window.DOMParser) {
			try { 
				dom = (new DOMParser()).parseFromString(xml, "text/xml"); 
			} 
			catch (e) { dom = null; }
		}
		else if (window.ActiveXObject) {
			try {
				dom = new ActiveXObject('Microsoft.XMLDOM');
				dom.async = false;
				if (!dom.loadXML(xml)) // parse error ..

					window.alert(dom.parseError.reason + dom.parseError.srcText);
			} 
			catch (e) { dom = null; }
		}
		else
			alert("cannot parse xml string!");
		return dom;
	}
};
var Convertor = {
	/*	This work is licensed under Creative Commons GNU LGPL License.

	License: http://creativecommons.org/licenses/LGPL/2.1/
   Version: 0.9
	Author:  Stefan Goessner/2006
	Web:     http://goessner.net/ 
*/
	json2xml: function(o, tab) {
		var toXml = function(v, name, ind) {
			var xml = "";
			if (v instanceof Array) {
				for (var i=0, n=v.length; i<n; i++)
					xml += ind + toXml(v[i], name, ind+"\t") + "\n";
			}
			else if (typeof(v) == "object") {
				var hasChild = false;
				xml += ind + "<" + name;
				for (var m in v) {
					if (m.charAt(0) == "@")
						xml += " " + m.substr(1) + "=\"" + v[m].toString() + "\"";
					else
						hasChild = true;
				}
				xml += hasChild ? ">" : "/>";
				if (hasChild) {
					for (var m in v) {
						if (m == "#text")
							xml += v[m];
						else if (m == "#cdata")
							xml += "<![CDATA[" + v[m] + "]]>";
						else if (m.charAt(0) != "@")
							xml += toXml(v[m], m, ind+"\t");
					}
					xml += (xml.charAt(xml.length-1)=="\n"?ind:"") + "</" + name + ">";
				}
			}
			else {
				xml += ind + "<" + name + ">" + v.toString() +  "</" + name + ">";
			}
			return xml;
		}, xml="";
		for (var m in o)
			xml += toXml(o[m], m, "");
		return tab ? xml.replace(/\t/g, tab) : xml.replace(/\t|\n/g, "");
	}, 
	// Changes XML to JSON
	//From http://davidwalsh.name/convert-xml-json
	xmlToJson: function(xml) {
	
		// Create the return object
		var obj = {};

		if (xml.nodeType == 1) { // element
			// do attributes
			if (xml.attributes.length > 0) {
				obj["@attributes"] = {};
				for (var j = 0; j < xml.attributes.length; j++) {
					var attribute = xml.attributes.item(j);
					obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
				}
			}
		} else if (xml.nodeType == 3) { // text
			obj = xml.nodeValue;
		}
		// do children
		if (xml.hasChildNodes()) {
			for(var i = 0; i < xml.childNodes.length; i++) {
				var item = xml.childNodes.item(i);
				var nodeName = item.nodeName;
				if (typeof(obj[nodeName]) == "undefined") {
					obj[nodeName] = Convertor.xmlToJson(item);
				} else {
					if (typeof(obj[nodeName].push) == "undefined") {
						var old = obj[nodeName];
						obj[nodeName] = [];
						obj[nodeName].push(old);
					}
					obj[nodeName].push(Convertor.xmlToJson(item));
				}
			}
		}
		return obj;
	}
};