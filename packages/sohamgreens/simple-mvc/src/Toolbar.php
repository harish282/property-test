<?php
	namespace SohamGreens\SimpleMvc;

	class Toolbar
	{
		public static $toolbuttons = array();
		
		static public function addButton($lable, $action, $image = '', $js = '')
		{
			self::$toolbuttons[] = array('label' => $lable, 'action' => $action, 'image' => $image, 'js' => $js);
		}
		
		static public function setButtons($toolbuttons)
		{
			self::$toolbuttons = $toolbuttons;
		}
		
		static public function show()
		{
			
			$html .= '<ul class="menubar">';

			foreach(self::$toolbuttons as $button)
			{
				$link = empty($button['js'])?'href=\''.$button['action'].'\'':'href=#;return false;onclick="'.$button['js'].'";return false;';
				$html .= '<li title="'.$button['label'].'">';
				
				if(!empty($button['image']))
					$html .= '<img src="images/toolbar/'.$button['image'].'" width="16" height="16"> ';
				
				$html .= '<a class="btn grey medium" '.$link.'><span>'.$button['label'].'</span></a></li>';
				
			}
			$html .= '</ul>';
			                    
			return $html;
		}

	}

?>