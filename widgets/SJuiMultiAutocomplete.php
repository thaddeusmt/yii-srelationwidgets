<?php
/**
 * SMultiAutocomplete class file.
 *

 */
Yii::import('zii.widgets.jui.CJuiAutoComplete');
/**
 * SMultiAutocomplete Creates Multiple Select Boxes
 *

 */
class SJuiMultiAutocomplete extends CJuiAutoComplete
{

  /**
	 * @var string the delimiter character that splits the multiple items
	 */
  public $delimiter = ',';

  /**
	 * @var int minimum number of characters before autocomplete starts
	 */
  public $minLength = 3;

  /**
	 * Initializes the widget.
	 * This method will publish JUI assets if necessary.
	 * It will also register jquery and JUI JavaScript files and the theme CSS file.
	 * Added for multiple support: some callback JS functions
	 */
  public function init() {
    parent::init(); // ensure assets are published
    // add a couple JS functions used by the callbacks
    $js = "function split( val ) {
        if(val) {
          return val.split( /{$this->delimiter}\s*/ );
        }
        return [];
      }
      function extractLast( term ) {
        if(term) {
          return split( term ).pop();
        }
        return [];
      }";
    Yii::app()->getClientScript()->registerScript(__CLASS__.'#functions', $js);
  }

  /**
	 * Run this widget.
	 * This method registers necessary javascript and renders the needed HTML code.
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

		if($this->hasModel())
			echo CHtml::activeTextField($this->model,$this->attribute,$this->htmlOptions);
		else
			echo CHtml::textField($name,$this->value,$this->htmlOptions);

    // set source callback
    if($this->sourceUrl!==null) {
      $url=CHtml::normalizeUrl($this->sourceUrl);
      $this->options['source']="js:function( request, response ) {
        $.getJSON( '{$url}', {
          term: extractLast( request.term )
        }, response );
      }";
    } else {
      $this->options['source']=$this->source;
    }

    // set search callback
    $this->options['search']="js:function() {
      var term = extractLast( this.value );
      if ( term.length < {$this->minLength} ) {
        return false;
      }
    }";  // show initial display value

    // set select callback
    $this->options['select']="js:function() {
      // prevent value inserted on focus
      return false;
		}";

    // set select callback
    $this->options['focus']="js:function(event, ui){
      var terms = split( this.value );
      // remove the current input
      terms.pop();
      // add the selected item
      terms.push( ui.item.value );
      // add placeholder to get the comma-and-space at the end
      terms.push( '' );
      this.value = terms.join( '{$this->delimiter} ' );
      return false;
    }";

		$options=CJavaScript::encode($this->options);

    $stopTab = '
      // don not navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
    ';

		$js = "jQuery('#{$id}'){$stopTab}.autocomplete($options);";

		$cs = Yii::app()->getClientScript();
		$cs->registerScript(__CLASS__.'#'.$id, $js);
	}

}
