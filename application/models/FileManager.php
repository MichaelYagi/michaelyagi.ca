<?php

class Application_Model_FileManager extends Zend_Db_Table_Abstract 
{ 
        
    public function getAllFileInfo($sort='filename') 
    {              
        try 
        {   
        	$path = new DirectoryIterator(APPLICATION_PATH.'/../public/media/');
        	
        	$arrFile = array();
        	$arrFileDetails = array();
        	
        	foreach ($path as $file) 
        	{
        		$filename = $file->getFilename();
        		if ($filename[0] != '.' && $file->isFile())
        		{
        			$pos_dot = strrpos($filename, "."); // find '.'

  					$extension = ($pos_dot !== false) ? substr($filename, $pos_dot+1) : null;
  					
        			$arrFileDetails['filename'] = $filename;
					$arrFileDetails['filesize'] = $file->getSize();
					$arrFileDetails['extension'] = $extension;
    				array_push($arrFile, $arrFileDetails);
    			}
			}
			
			$this->sortBy($arrFile, $sort);			
			
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $arrFile; 
    }
    
    public function setDeleteFiles($fileList = null) 
    {              
        try 
        {   
        	$retVal = -1;
        	foreach($fileList as $value)
        	{
        		if (file_exists(APPLICATION_PATH.'/../public/media/'.$value))
        		{
        			unlink(APPLICATION_PATH.'/../public/media/'.$value);
        			$retVal = 1;
        		}
        		else
        		{
        			$retVal = -1;
        			break;
        		}
        	}
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal; 
    }
    
    public function getJrcMedia() 
    {              
    	$langSess = new Zend_Session_Namespace('language'); 
		$translate = $langSess->translate;

    	//return array with translated titles
        $trackArray = array(
        					array(
        						'no'=>1,
        						'title'=>$translate->_('tracktitle_1'),
        						'mp3'=>'/media/01_Prelude.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=uNH1PyWcBa0'
        						),
        					array(
        						'no'=>2,
        						'title'=>$translate->_('tracktitle_2'),
        						'mp3'=>'/media/02_Fugue_in_F_minor,_Bk._I,_No._12,_BWV_857.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=YBHdCP5yL5o'
        						),
        					array(
        						'no'=>3,
        						'title'=>$translate->_('tracktitle_3'),
        						'mp3'=>'/media/03_Sonata_No._5-I._Allegro.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=Ya8iShbxF9E'
        						),
        					array(
        						'no'=>4,
        						'title'=>$translate->_('tracktitle_4'),
        						'mp3'=>'/media/04_Sonata_No._5-II._Andante.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=cQHn0997Rk4'
        						),
        					array(
        						'no'=>5,
        						'title'=>$translate->_('tracktitle_5'),
        						'mp3'=>'/media/05_Sonata_No._5-III._Presto.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=rwdnxniK0-w'
        						),
        					array(
        						'no'=>6,
        						'title'=>$translate->_('tracktitle_6'),
        						'mp3'=>'/media/06_Petite_Suite-I._En_bateau.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=rfJnRt7hXwM'
        						),
        					array(
        						'no'=>7,
        						'title'=>$translate->_('tracktitle_7'),
        						'mp3'=>'/media/07_Petite_Suite-Ii._Cortege.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=zoyxrsp4ZlA'
        						),
        					array(
        						'no'=>8,
        						'title'=>$translate->_('tracktitle_8'),
        						'mp3'=>'/media/08_Petite_Suite-III._Menuet.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=kMDE3KC5e5c'
        						),
        					array(
        						'no'=>9,
        						'title'=>$translate->_('tracktitle_9'),
        						'mp3'=>'/media/09_Petite_Suite-IV._Ballet.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=A-aOZ-wVaXo'
        						),
        					array(
        						'no'=>10,
        						'title'=>$translate->_('tracktitle_10'),
        						'mp3'=>'/media/10_Liebesleid.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=YMcyqK3yN8s'
        						),
        					array(
        						'no'=>11,
        						'title'=>$translate->_('tracktitle_11'),
        						'mp3'=>'/media/11_Oblivion.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=kB7q0U6Y7jQ'
        						),
        					array(
        						'no'=>12,
        						'title'=>$translate->_('tracktitle_12'),
        						'mp3'=>'/media/12_Libertango.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=BMUFGyvSl-E'
        						),
        					array(
        						'no'=>13,
        						'title'=>$translate->_('tracktitle_13'),
        						'mp3'=>'/media/13_Five_Pieces_for_Two_Violins_(Arr._Atovmian)-I._Prelude.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=YTDUidowhbU'
        						),
        					array(
        						'no'=>14,
        						'title'=>$translate->_('tracktitle_14'),
        						'mp3'=>'/media/14_Five_Pieces_for_Two_Violins_(Arr._Atovmian)-II._Gavotte.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=buWuD0QojFg'
        						),
        					array(
        						'no'=>15,
        						'title'=>$translate->_('tracktitle_15'),
        						'mp3'=>'/media/15_Five_Pieces_for_Two_Violins_(Arr._Atovmian)-III._Elegy.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=12eNb95orLI'
        						),
        					array(
        						'no'=>16,
        						'title'=>$translate->_('tracktitle_16'),
        						'mp3'=>'/media/16_Five_Pieces_for_Two_Violins_(Arr._Atovmian)-IV._Waltz.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=OtEwxOtFubg'
        						),
        					array(
        						'no'=>17,
        						'title'=>$translate->_('tracktitle_17'),
        						'mp3'=>'/media/17_Five_Pieces_for_Two_Violins_(Arr._Atovmian)-V._Polka.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=VWVzBMBtDHI'
        						),
        					array(
        						'no'=>18,
        						'title'=>$translate->_('tracktitle_18'),
        						'mp3'=>'/media/18_Mephisto-Waltz_No._1.mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=ZN69j2XgM3Q'
        						),
        					array(
        						'no'=>19,
        						'title'=>$translate->_('tracktitle_19'),
        						'mp3'=>'/media/19_Furusato_(Arr._Atsufumi_Morita).mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=UKBMsMtPb-s'
        						),
        					array(
        						'no'=>20,
        						'title'=>$translate->_('tracktitle_20'),
        						'mp3'=>'/media/20_Akatombo_(Arr._Atsufumi_Morita).mp3',
        						'youtube'=>'http://www.youtube.com/watch?v=6vAeUyOQG2Q'
        						)
        						
        					);
        
        return $trackArray; 
    }
    
    //Sort a multidimensional array by value
    //Pass by reference
    function sortBy(&$arr, $col, $dir = SORT_ASC) 
    {
    	$sort_col = array();
    	foreach ($arr as $key=> $row) 
    	{
        	$sort_col[$key] = $row[$col];
    	}

    	array_multisort($sort_col, $dir, $arr);
	}


    
}