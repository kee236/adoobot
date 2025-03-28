<?php

class Xtra extends CI_Controller
{
	public $a; 

    public function __construct()
    {
		parent::__construct();
		if ($this->session->userdata('logged_in') != 1)
		redirect('home/login_page', 'location');        
		if ($this->session->userdata('user_type') != 'Admin')
		redirect('home/login_page', 'location'); 

		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 3000); 
    }
	

	function translation()
	{
		$system_lang_file=array('calendar','date','db','email','form_validation','ftp','imglib','migration','number','pagination','profiler','unit_test','upload');
		$this->load->helper('language');
		foreach ($system_lang_file as $sys_lang) {
			$this->lang->load($sys_lang, "english");
		}

	    $all_system_lang_array=$this->lang->language;

		$folder_path="application/";
		$all_directory= $this->scanAll($folder_path);
		
		$all_lang=array();
		
		foreach($all_directory as $dir){
			if(str_starts_with($dir, 'application')){
				$dir = str_replace(['application/',"application\\"], '', $dir);
			}
			$dir = APPPATH.$dir;
			$content=file_get_contents($dir);
			preg_match_all('#lang->line\("(.*?)"\)#si', $content, $matches);	
			
			foreach($matches[1] as $line){
				$all_lang[]=strtolower($line);
			}
			
			preg_match_all('#lang->line\(\'(.*?)\'\)#si', $content, $matches1);	
			
			foreach($matches1[1] as $line){
				$all_lang[]=strtolower($line);
			}
			
		}

		// echo "<pre>";print_r($all_lang); exit();
		
		/*** Get all existing language from language folder ***/
		
		$language_name_array=array("arabic","english","bengali","dutch","french","german","greek","italian","portuguese","russian","spanish","turkish","vietnamese");
		
		
		foreach($language_name_array as $language_name){
		
		$this->lang->is_loaded = array();
		$this->lang->language = array();
		
	 	$path=str_replace('\\', '/', APPPATH.'/language/'.$language_name); 
        $files=$this->lang_scanAll($path);
        foreach ($files as $key2 => $value2) 
        {
            $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
            if($current_file=="" || !is_file($current_file)) continue;
            $current_file_explode=explode('/',$current_file);
            $filename=array_pop($current_file_explode);
            $pos=strpos($filename,'_lang.php');
            if($pos!==false) // check if it is a lang file or not
            {
                $filename=str_replace('_lang.php', '', $filename); 
                $this->lang->load($filename, $language_name);
            }
        }      
		      
		
		$all_lang_prev_array=$this->lang->language;
		
		$all_lang_prev_array=array_change_key_case($all_lang_prev_array, CASE_LOWER);
		
		foreach($all_lang as $lang_index){

			//$filter_lang_index=str_replace(array('"',"'"), "`",$lang_index);
		
			// if(isset($all_lang_prev_array[$filter_lang_index]))
			// 	$now_all_write_lang[$filter_lang_index]=$all_lang_prev_array[$filter_lang_index];
			// else
			// 	$now_all_write_lang[$filter_lang_index]="";.

			// alamin

			if(isset($all_lang_prev_array[$lang_index]))
			{
				$filter_lang_index=str_replace(array('"',"'"), "`",$all_lang_prev_array[$lang_index]);
				$now_all_write_lang[$lang_index]=$filter_lang_index;
			}
			else
				$now_all_write_lang[$lang_index]="";
		}
		
		
		/** Language that's exist but not found in current code **/
		
		$extra_lang= array_diff_key($all_lang_prev_array,$now_all_write_lang);
		$now_all_write_lang_merge_prev = array_merge($now_all_write_lang, $extra_lang);
		$now_all_write_lang_merge = array_diff_key($now_all_write_lang_merge_prev,$all_system_lang_array);


		asort($now_all_write_lang_merge);
		
		
		$lang_write_file=$path."/v_9_5_lang.php";
		
		
		// if(file_exists($lang_write_file)){
		// 	$date=date("Y-m-d H-i-s");
		// 	$write_path="backup_lang/{$language_name}/all_lang_{$date}.php";
		// 	copy($lang_write_file,$write_path);
		// }
		
		file_put_contents($lang_write_file, '<?php $lang = ' . var_export($now_all_write_lang_merge, true) . ';');

		/*IF WE WANT MULTIPLE CHUNKED FILES*/
		/*$char_array = array("a","b","c","d","e","f","g","h","i","j","k","l","m",'n','o');	
		$chunk_lang = array_chunk($now_all_write_lang_merge, 400, true);
		$i=0;
		foreach ($chunk_lang as $key => $value) 
		{
			
			$v = $char_array[$i];
			$temp_lang_write_file  = $path."/v_1_0_".$v."_lang.php";
			file_put_contents($temp_lang_write_file, '<?php $lang = ' . var_export($value, true) . ';');	
			$i++;	
		} */
		
		
		
		$new_lang= array_diff($now_all_write_lang_merge,$all_lang_prev_array);
		
		echo $language_name.": New Line added : ". count($new_lang)."<br>";
				
		}
		
		
	}

 
  
  	function scanAll($myDir){

		$dirTree = array();
		$di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);
		
		foreach (new RecursiveIteratorIterator($di) as $filename) {
		
		$dir = str_replace($myDir, '', dirname($filename));
		//$dir = str_replace('/', '>', substr($dir,1));
		
		$org_dir=str_replace("\\", "/", $dir);
		
		
		if($org_dir)
		$file_path = $org_dir. "/". basename($filename);
		else
		$file_path = basename($filename);
		$dirTree[] = $file_path;
		
		}
		
		return $dirTree;
		
	}

  	public function lang_scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }

	
	
  
  
  
  
	
}



?>