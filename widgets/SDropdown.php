<?php
/**
 * SMultiAutocomplete class file.
 *

 */
/**
 * SMultiAutocomplete Creates Multiple Select Boxes
 *
 */
class SDropdown extends CInputWidget
{

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

    if(isset($this->htmlOptions['multiple']))
      unset($this->htmlOptions['multiple']);

		if($this->hasModel())
			echo CHtml::activeDropDownList(
					$this->model,
					$this->attribute,
					$this->value,
					$this->htmlOptions);
		else
			echo CHtml::dropDownList(
          $name,
					$this->value,
					$this->data,
					$this->htmlOptions);
	}

}
