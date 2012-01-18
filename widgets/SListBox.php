<?php
/**
 * SMultiAutocomplete class file.
 *

 */
/**
 * SMultiAutocomplete Creates Multiple Select Boxes
 *

 */
class SListBox extends CInputWidget
{
  /**
	 * @var array the list of all of the options
	 */
	public $data;

  /**
	 * Run this widget.
	 * This method renders the needed HTML code.
	 */
	public function run()
	{
		list($name,$id)=$this->resolveNameID();
		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];

    if(isset($this->data['']))  // remove the "blank" option, if there is one, since you can unselect it
      unset($this->data['']);

		if($this->hasModel())
			echo CHtml::ActiveListBox(
					$this->model,
					$this->attribute,
					$this->data,
					$this->htmlOptions);
    else
      echo CHtml::ListBox(
          $name,
					$this->value, // keys to select
          $this->data, // data
					$this->htmlOptions);
	}

}
