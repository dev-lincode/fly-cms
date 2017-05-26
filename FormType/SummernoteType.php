<?php
namespace Lincode\Fly\Bundle\FormType;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SummernoteType extends TextareaType {
    public function configureOptions(OptionsResolver $resolver)
    {
		$resolver->setDefaults(array(
			"required"		=> false,
			"bold" 			=> true,
			"italic" 		=> true,
			"underline" 	=> true,
			"style"			=> true,
			"fontname"		=> false,
			"clear" 		=> true,
			"strikethrough" => true,
			"superscript" 	=> true,
			"subscript" 	=> true,
			"fontsize" 		=> false,
			"color"			=> false,
			"ul"			=> true,
			"ol"			=> true,
			"paragraph"		=> true,
			"height"		=> true,
			"picture"		=> true,
			"link"			=> true,
			"video"			=> true,
			"table"			=> true,
			"hr"			=> true,
			"fullscreen"	=> true,
			"codeview"		=> true,
			"undo"			=> true,
			"redo"			=> true,
			"help"			=> false
		));
	}

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		//groupStyle
		$view->vars['style'] 		= $options['style'];
		
		//groupFormat
		$view->vars['bold'] 		= $options['bold'];
		$view->vars['italic'] 		= $options['italic'];
		$view->vars['underline'] 	= $options['underline'];
		$view->vars['superscript']  = $options['superscript'];
		$view->vars['subscript'] 	= $options['subscript'];
		$view->vars['strikethrough']= $options['strikethrough'];
		$view->vars['clear'] 		= $options['clear'];
		
		//groupFont
		$view->vars['fontname'] 	= $options['fontname'];
		
		//groupColor
		$view->vars['color']		= $options['color'];

		//groupFontsize
		$view->vars['fontsize']		= $options['fontsize'];

		//groupPara
		$view->vars['ul']			= $options['ul'];
		$view->vars['ol']			= $options['ol'];
		$view->vars['paragraph']	= $options['paragraph'];

		//groupHeight
		$view->vars['height']		= $options['height'];

		//groupTable
		$view->vars['table']		= $options['table'];

		//groupInsert
		$view->vars['link']			= $options['link'];
		$view->vars['picture']		= $options['picture'];
		$view->vars['video']		= $options['video'];
		$view->vars['hr']			= $options['hr'];

		//groupMisc
		$view->vars['fullscreen']	= $options['fullscreen'];
		$view->vars['codeview']		= $options['codeview'];
		$view->vars['undo']			= $options['undo'];
		$view->vars['redo']			= $options['redo'];
		$view->vars['help']			= $options['help'];

		if(empty($options['groupStyle'])) {
			$groupStyle = "";

			if($options['style'])
				$groupStyle .= (strlen($groupStyle) > 0 ? "," : "") . "'style'";
		}

		if(empty($options['groupFormat'])) {
			$groupFormat = "";
		
			if($options['bold'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'bold'";

			if($options['italic'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'italic'";

			if($options['underline'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'underline'";

			if($options['superscript'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'superscript'";

			if($options['subscript'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'subscript'";

			if($options['strikethrough'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'strikethrough'";

			if($options['clear'])
				$groupFormat .= (strlen($groupFormat) > 0 ? "," : "") . "'clear'";
		}

		if(empty($options['groupFont'])) {
			$groupFont = "";
		
			if($options['fontname'])
				$groupFont .= (strlen($groupFont) > 0 ? "," : "") . "'fontname'";
		}

		if(empty($options['groupColor'])) {
			$groupColor = "";

			if($options['color'])
				$groupColor .= (strlen($groupColor) > 0 ? "," : "") . "'color'";

		}

		if(empty($options['groupFontsize'])) {
			$groupFontsize = "";

			if($options['fontsize'])
				$groupFontsize .= (strlen($groupFontsize) > 0 ? "," : "") . "'fontsize'";

		}

		if(empty($options['groupPara'])) {
			$groupPara = "";

			if($options['ul'])
				$groupPara .= (strlen($groupPara) > 0 ? "," : "") . "'ul'";

			if($options['ol'])
				$groupPara .= (strlen($groupPara) > 0 ? "," : "") . "'ol'";

			if($options['paragraph'])
				$groupPara .= (strlen($groupPara) > 0 ? "," : "") . "'paragraph'";

		}

		if(empty($options['groupHeight'])) {
			$groupHeight = "";

			if($options['height'])
				$groupHeight .= (strlen($groupHeight) > 0 ? "," : "") . "'height'";
		}

		if(empty($options['groupTable'])) {
			$groupTable = "";

			if($options['height'])
				$groupTable .= (strlen($groupTable) > 0 ? "," : "") . "'table'";
		}

		if(empty($options['groupInsert'])) {
			$groupInsert = "";

			if($options['link'])
				$groupInsert .= (strlen($groupInsert) > 0 ? "," : "") . "'link'";

			if($options['picture'])
				$groupInsert .= (strlen($groupInsert) > 0 ? "," : "") . "'picture'";

			if($options['video'])
				$groupInsert .= (strlen($groupInsert) > 0 ? "," : "") . "'video'";

			if($options['hr'])
				$groupInsert .= (strlen($groupInsert) > 0 ? "," : "") . "'hr'";
		}

		if(empty($options['groupMisc'])) {
			$groupMisc = "";

			if($options['fullscreen'])
				$groupMisc .= (strlen($groupMisc) > 0 ? "," : "") . "'fullscreen'";

			if($options['codeview'])
				$groupMisc .= (strlen($groupMisc) > 0 ? "," : "") . "'codeview'";
			
			if($options['undo'])
				$groupMisc .= (strlen($groupMisc) > 0 ? "," : "") . "'undo'";

			if($options['redo'])
				$groupMisc .= (strlen($groupMisc) > 0 ? "," : "") . "'redo'";

			if($options['help'])
				$groupMisc .= (strlen($groupMisc) > 0 ? "," : "") . "'help'";
		}

		$view->vars['groupStyle'] 	= $groupStyle;
		$view->vars['groupFormat'] 	= $groupFormat;
		$view->vars['groupFont']    = $groupFont;
		$view->vars['groupColor'] 	= $groupColor;
		$view->vars['groupFontsize']= $groupFontsize;
		$view->vars['groupPara'] 	= $groupPara;
		$view->vars['groupHeight'] 	= $groupHeight;
		$view->vars['groupTable']   = $groupTable;
		$view->vars['groupInsert'] 	= $groupInsert;
		$view->vars['groupMisc'] 	= $groupMisc;
	}

    public function getBlockPrefix()
    {
        return 'summernote';
    }
}