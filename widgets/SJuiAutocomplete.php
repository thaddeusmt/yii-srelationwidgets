<?php
/**
 * SMultiAutocomplete class file.
 *

 */
Yii::import('zii.widgets.jui.CJuiAutoComplete');
/**
 * SMultiAutocomplete Creates a JUI Autocomplete text field
 *
 */
class SJuiAutocomplete extends CJuiAutoComplete
{
  /**
	 * @var int minimum number of characters before autocomplete starts
	 */
  public $minLength = 3;

  /**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
	 */
	public function run()
	{
    // set search callback
    $this->options['search']="js:function() {
      if ( this.value.length < {$this->minLength} ) {
        return false;
      }
    }";  // show initial display value

    parent::run();
	}

}
