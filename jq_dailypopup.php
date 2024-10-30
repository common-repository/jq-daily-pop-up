<?php
/*
Plugin Name: JQ - DailyPopUp
Plugin URI: http://www.yannicktanguy.com/pour-wordpress/documentations/daily-pop-pour-wordpress
Description: Ce module vous permet d afficher un pop up plein &eacute;can avec un information centr&eacute;e une fois par jour.</p><p>Vous avez l obligation d avoir le framework JQuery install&eacute; avant de pouvoir utiliser le module, aussi le module doit etre placer en position absolute ou dans une position qui ne dérange pas la lisibilit&eacute; du site
Author: Yannick TANGUY
Version: 1.3
Author URI: http://www.yannicktanguy.com
*/

define('FORM_PLUGIN_URL', plugin_dir_url( __FILE__ ));

error_reporting(E_ALL);

class jQDailyPopUp extends WP_Widget {
	
	function jQDailyPopUp()
	{
		parent::WP_Widget(false, $name = 'jQ Daily PopUp', array('name' => 'jQ Daily PopUp', 'description' => 'Full Screen Pop Up'));
	}
	
	function widget($args, $instance)
	{
		function recursiveDelete($str){
			if(is_file($str)){ return @unlink($str); }
			elseif(is_dir($str)){
				$scan = glob(rtrim($str,'/').'/*');
				foreach($scan as $index=>$path){ recursiveDelete($path); }
				return @rmdir($str);
			}
		}

		extract($args);
		
		//Get IP address
		$ipduclient=$_SERVER["REMOTE_ADDR"];
		
		// Today
		$moisjour=date('d');
		
		//Yesterday
		$hiertmp= time() - ( 24 * 60 * 60);
		$hierjour = date('d', $hiertmp);
		
		$repert=getcwd().'/wp-content/plugins/wp_dailypopup_Widget/cache/';
		// Test root temp dailypopup module directory and create clear index.html
		if (!file_exists($repert)) { mkdir ($repert, 0777); }
		if (!file_exists($repert.'index.html')) {
			$fpa=fopen($repert.'index.html',"a+");
			fclose($fpa);
		}
	
		// Test the day dailypopup module directory
		if (!file_exists($repert.$moisjour)) {
			mkdir ($repert.$moisjour, 0777); 
		}
		
		if (!file_exists($repert.$moisjour.'/index.html')) {
			$fpb=fopen($repert.$moisjour.'/index.html',"a+");
			fclose($fpb);
		}
		
		// Test yesterday dailypopup module directory and delete all files
		if (file_exists($repert.'/'.$hierjour)) {
			recursiveDelete($repert.'/'.$hierjour);
		}
		
		// Name of de ip File to inclide into day's directory
		$fichp=$repert.$moisjour.'/'.$ipduclient;
	
		//Debug Mode
		if ($instance['radebug']==1){
			$ajourdtmp= time();
			$aujourdjour = date('d', $ajourdtmp);
			if (file_exists($repert.'/'.$aujourdjour)) {
				recursiveDelete($repert.'/'.$aujourdjour);
			}
		}
		//echo $instance['radebug'];
	
		if (!file_exists($fichp)) {	
			// Include Style & Script
			wp_register_style( 'prefix-style', plugins_url('/style/css.css', __FILE__) );
			wp_register_style( 'prefix-style-select', plugins_url('/style/'.$instance['optstyle'].'/css.css', __FILE__) );
			wp_enqueue_style( 'prefix-style' );
			wp_enqueue_style( 'prefix-style-select' );
			wp_register_script( 'jqdailypopjs', plugins_url('/js/js.js', __FILE__) );
			wp_enqueue_script( 'jqdailypopjs' );
			$page_id=$instance['selectpage'];
			$page_data = get_page( $page_id );
			$letexte=apply_filters('the_content', $page_data->post_content);
			echo $lalargeur=$instance['optlargeur'];
			if ($instance['radebug']==0){
				$fpc=fopen($fichp,"a+");
				fclose($fpc);
			}
			$passpas='1';
			$text='<div id="dailyfullscreen"><div id="dailyposition">'.$letexte.'</div><div class="posdailybut"><input type="button" class="dailybutton" align="center" id="closedailyp" value="'.$instance['optbtntext'].'"><div id="actdaily"></div></div>';
			echo '<div id="dailycomplete">'.$text.'<input type="hidden" id="dailypopupwidth" value="'.$lalargeur.'"></div>';
		} 
		else { $passpas='0'; }
	}
	
	function update($new_instance, $old_instance)
    {
  		$instance = $old_instance;
 
		//Récupération des paramètres envoyés
		$instance['optstyle'] = $new_instance['optstyle'];
		$instance['selectpage'] = $new_instance['selectpage'];
		$instance['optlargeur'] = $new_instance['optlargeur'];
		$instance['optbtntext'] = $new_instance['optbtntext'];
		$instance['radebug'] = $new_instance['radebug'];
	 
		return $instance;
    }
	function form($instance)
	{
        $optstyle = @esc_attr($instance['optstyle']);
		$selectpage = @esc_attr($instance['selectpage']);
		$optlargeur = @esc_attr($instance['optlargeur']);
		$optbtntext = @esc_attr($instance['optbtntext']);
		$radebug = @esc_attr($instance['radebug']);
        if (!$optstyle){ $optstyle="black"; }
        if (!$selectpage){ $selectpage=""; }
        if (!$optlargeur){ $optlargeur="800"; }
        if (!$optbtntext){ $optbtntext="Close"; }
        if (!$radebug){ $radebug="0"; }
		?>
		<ul class="adminformlist">
			<li>
				<label for="<?php echo $this->get_field_id('optstyle'); ?>">Choose style</label>
				<select name="<?php echo $this->get_field_name('optstyle'); ?>" id="<?php echo $this->get_field_id('optstyle'); ?>">
					<option <?php if ( $optstyle=="black") { ?>selected="selected"<?php } ?> value="black">Black</option>
					<option <?php if ( $optstyle=="red") { ?>selected="selected"<?php } ?> value="red">Red</option>
					<option <?php if ( $optstyle=="blue") { ?>selected="selected"<?php } ?> value="blue">Blue</option>
					<option <?php if ( $optstyle=="green") { ?>selected="selected"<?php } ?> value="green">Green</option>
					<option <?php if ( $optstyle=="yellow") { ?>selected="selected"<?php } ?> value="yellow">Yellow</option>
					<option <?php if ( $optstyle=="white") { ?>selected="selected"<?php } ?> value="white">White</option>
					<option <?php if ( $optstyle=="alpha") { ?>selected="selected"<?php } ?> value="alpha">Alpha</option>
				</select>
			</li>
			<li>
				<hr>
			</li>
			<li>
				<label>Choose article :</label>
				<select name="<?php echo $this->get_field_name('selectpage'); ?>" id="<?php echo $this->get_field_id('selectpage'); ?>" > 
					 <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option> 
					 <?php 
					  $pages = get_pages(); 
					  foreach ( $pages as $page ) {
						$idpage=$page->ID;
						if ($selectpage==$idpage){ $selectpage='selected="selected"'; }  else  { $selectpage=''; }
						$option = '<option '.$selectpage.' value="' . $idpage . '">';
						$option .= $page->post_title;
						$option .= '</option>';
						echo $option;
					  }
					 ?>
				</select>
			</li>
			<li>
				<hr>
			</li>
			<li>
				<label>Width (px)</label>
				<input  name="<?php echo $this->get_field_name('optlargeur'); ?>" id="<?php echo $this->get_field_id('optlargeur'); ?>" type="text" value="<?php echo $optlargeur; ?>">
			</li>
			<li>
				<label>Text on Close button</label>
				<input type="text" name="<?php echo $this->get_field_name('optbtntext'); ?>" id="<?php echo $this->get_field_id('optbtntext'); ?>" value="<?php echo $optbtntext; ?>">
			</li>
			<li>
				<label>DEBUG Mode</label>
				<fieldset class="radio">
					<input type="radio" <?php if ($radebug=="1"){ ?>checked="checked"<?php } ?> value="1"  name="<?php echo $this->get_field_name('radebug'); ?>">
					<label>ON</label>
					<input type="radio" <?php if ($radebug=="0"){ ?>checked="checked"<?php } ?> value="0"  name="<?php echo $this->get_field_name('radebug'); ?>">
					<label>OFF</label>
				</fieldset>
			</li>
		</ul>
		
	<?php
		
	}
}
function jQdaily_register_widget() {
	  register_widget( 'jQDailyPopUp' );
}
add_action('widgets_init', 'jQdaily_register_widget');

?>