/*
Written by Ndimangwa Fadhili, assumpsion
function validate , will check if the control has a validate attribute set,

then validate_message, validate_control and validate_expression has to be set
even if it is a select/drop box, validate_expression should be set for compatibility
you need to require_once language.php
<input type="text" validate="true" validate_control="text" validate_expression="<?= $expr1 ?>%<?= $expr2 ?>...%<?= $exprn ?>" validate_message="<?= $msg1 ?>%..%<?= $msgj ?>"/>
*/
var Validation = {
	validate: function(control1, target1)	{
		if (! (control1 && target1)) return false;
		if (! control1.getAttribute('validate')) return true; /*Control need not be validated*/
		if (! (control1.getAttribute('validate_control') && control1.getAttribute('validate_expression') && control1.getAttribute('validate_message'))) return false;
		var expressionArray1 = control1.getAttribute('validate_expression');
		expressionArray1 = expressionArray1.split("%");
		var messageArray1 = control1.getAttribute('validate_message');
		messageArray1 = messageArray1.split("%");
		var _defaultMessage = messageArray1[0]; //In case messageArray1 is < than expressionArray1
		if (! control1.disabled && ((new String(control1.getAttribute('validate_control'))).valueOf() == (new String('text')).valueOf()))	{
			var _val = (new String(control1.value)).trim().valueOf();
			if (control1.getAttribute('notrequired') && (_val == (new String('')).trim().valueOf())) return true;
			var isStillValid = true;
			for (var i=0; i < expressionArray1.length; i++)	{
				var expr1 = expressionArray1[i];
				var msg1 = _defaultMessage;
				if (messageArray1[i])	{ msg1 = messageArray1[i]; }
				var regex1 = new RegExp(expr1);
				if (! regex1) {
					target1.innerHTML = "";
					var span1 = document.createElement('span');
					span1.appendChild(document.createTextNode('Regular Expression Failed'));
					target1.appendChild(span1);
					return false;				
				}
				var bln = _val.match(regex1);
				if (! bln)	{
					target1.innerHTML = "";
					var span1 = document.createElement('span');
					span1.appendChild(document.createTextNode(msg1));
					target1.appendChild(span1);
					isStillValid = false;
					break;
				}				
			}
			return isStillValid;
		} else if (! control1.disabled && ((new String(control1.getAttribute('validate_control'))).valueOf() == (new String('select')).valueOf()))	{
			//For select box we expect to have only one item in the array,
			//For compatibility we pack this to the array too, however it were not
			//necessary to go this way
			var bln = (control1.options[control1.selectedIndex].value != "_@32767@_");
			if (! bln)	{
				target1.innerHTML = "";
				var span1 = document.createElement("span");
				span1.appendChild(document.createTextNode(_defaultMessage));
				target1.appendChild(span1);
			}
			return bln;
		} else	{
			return true; /* Extra Controls */
		}
	}
}