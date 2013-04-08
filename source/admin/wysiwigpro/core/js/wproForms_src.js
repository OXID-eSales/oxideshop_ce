// A class for accessing and manipulating radio and checkbox groups
// based on: http://www.breakingpar.com/bkp/home.nsf/0/CA99375CC06FB52687256AFB0013E5E9
function wproForms () {}
	// returns the currently selected radio button 
wproForms.prototype.getSelectedRadio = function (buttonGroup) {
   if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
	  for (var i=0; i<buttonGroup.length; i++) {
		 if (buttonGroup[i].checked) {
			return buttonGroup[i];
		 }
	  }
   } else {
	  if (buttonGroup.checked) { return buttonGroup; } // if the one button is checked, return zero
   }
   // if we get to this point, no radio button is selected
   return false;
}
	
wproForms.prototype.getSelectedRadioValue = function (buttonGroup) {
   // returns the value of the selected radio button or "" if no button is selected
   var button;
   if (button = this.getSelectedRadio(buttonGroup)) {
		return button.value;
   }
   return false;
}
	
	// goes through the radio buttons and checks the one with the correct value
wproForms.prototype.selectRadio = function (buttonGroup, value) {
	if (buttonGroup[0]) { // if the button group is an array (one button is not an array)
	  for (var i=0; i<buttonGroup.length; i++) {
		 if (buttonGroup[i].value == value) {
			buttonGroup[i].checked = true;
		} else {
			buttonGroup[i].checked = false;
		}
	  }
   } else {
		if (buttonGroup.value == value) {
			buttonGroup.checked = true;
		} else {
			buttonGroup.checked = false;
		}
   }
}
// returns indexes for the selected checkboxes	
wproForms.prototype.getSelectedCheckbox = function (buttonGroup) {
   // Go through all the check boxes. return an array of all the ones
   // that are selected. if no boxes were checked,
   // returned array will be empty (length will be zero)
   var retArr = new Array();
   var lastElement = 0;
   if (buttonGroup[0]) { // if the button group is an array (one check box is not an array)
	  for (var i=0; i<buttonGroup.length; i++) {
		 if (buttonGroup[i].checked) {
			retArr.length = lastElement;
			retArr[lastElement] = i;
			lastElement++;
			//retArr.push(
		 }
	  }
   } else { // There is only one check box (it's not an array)
	  if (buttonGroup.checked) { // if the one check box is checked
		 retArr.length = lastElement;
		 retArr[lastElement] = 0; // return zero as the only array value
	  }
   }
   return retArr;
} 
	
wproForms.prototype.getSelectedCheckboxValue = function (buttonGroup) {
   // return an array of values selected in the check box group. if no boxes
   // were checked, returned array will be empty (length will be zero)
   var retArr = new Array(); // set up empty array for the return values
   var selectedItems = this.getSelectedCheckbox(buttonGroup);
   if (selectedItems.length != 0) { // if there was something selected
	  //retArr.length = selectedItems.length;
	  for (var i=0; i<selectedItems.length; i++) {
		 if (buttonGroup[selectedItems[i]]) { // Make sure it's an array
			retArr.push(buttonGroup[selectedItems[i]].value);
		 } else { // It's not an array (there's just one check box and it's selected)
			retArr.push(buttonGroup.value);// return that value
		 }
	  }
   }
   return retArr;
}

wproForms.prototype.getElementValues = function (group) {
	var l = group.length
	var retArr = []
	if (l) {
		 for (var i=0; i<group.length; i++) {
			if (group[i]) {
				retArr.push(group[i].value);
			} else {
				retArr.push(group.value);
			}
		 }
	} else {
		retArr.push(group.value);
	}	
	return retArr;
}