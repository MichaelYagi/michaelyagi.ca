<?php

class Application_Model_Blog extends Zend_Db_Table_Abstract 
{ 
        
    public function Application_Model_Blog() 
    {
    } 
        
	public function getBlogPosts($activeonly=1, $order="post") 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	//No parameters to bind
        	$data = $this->getDefaultAdapter()->prepare("CALL spGetBlogPosts(?,?)");
        	$data->bindParam(1, $activeonly);
        	$data->bindParam(2, $order);
        	$data->execute();
        	//fetchAll into an array
        	$result = $data->fetchAll();
        	$data->closeCursor();
        	*/     	
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if ($activeonly == 1)
        	{
        		$SQL .= 'SELECT b.id as id, b.title as title, b.post as post, b.created as created, b.modified as modified, b.active as isActive, IFNULL(c.total, 0) commentcount FROM blog b LEFT JOIN (SELECT COUNT(*) total, blogid FROM comment WHERE active = 1 GROUP BY blogid) c ON (c.blogid = b.id) WHERE b.active = 1 ORDER BY b.id DESC';
        	}
        	else
        	{
        		$SQL .= 'SELECT b.id as id, b.title as title, b.post as post, b.created as created, b.modified as modified, b.active as isActive FROM blog b ORDER BY ';
        		if ($order == 'post')
        		{
        			$SQL .= 'b.id DESC';
        		}
        		else
        		{
        			$SQL .= 'b.active';
        		}
        	}
        	
        	if(!empty($SQL))
        	{
        		$result = $db->fetchAll($SQL);
            }
            else
            {
            	$result = 'Error';
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $result; 
    }
    
    public function getBlogPost($id=null,$activeonly=1) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spGetBlogPost(?,?)");
        	$data->bindParam(1, $id);
        	$data->bindParam(2, $showActive);
        	$data->execute();
        	$result = $data->fetch();
        	$data->closeCursor();  
        	*/

        	$db = Zend_Db_Table::getDefaultAdapter();
        	$SQL = '';
        	
        	if (isset($id) && is_numeric($id))
        	{
				$SQL .= 'SELECT b.id as id, b.title as title, b.post as post, b.created as created, b.modified as modified, b.active as isActive, IFNULL(c.total, 0) commentcount FROM blog b LEFT JOIN (SELECT COUNT(*) total, blogid FROM comment WHERE active = 1 GROUP BY blogid) c ON (c.blogid = b.id) WHERE b.id = '.$id;
        		
        		if ($activeonly == 1)
        		{
        			$SQL .= ' AND b.active = 1';
        		}
        	}

        	if(!empty($SQL))
        	{
        		$result = $db->fetchRow($SQL);
            }
            else
            {
            	$result = 'Error';
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
                
        return $result; 
    }
    
    public function setNewBlogPost($title,$post,$active) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spSetNewBlogPost(?,?,?,@retVal)");
        	$data->bindParam(1, $title);
        	$data->bindParam(2, $post);
        	$data->bindParam(3, $active);
        	$data->execute();
        	//Get the return value: < 0: Success, 0: Already Exists, -1: Invalid Inputs
			$sp = $this->getDefaultAdapter()->prepare("SELECT @retVal as ID");
			$sp->execute();
			$result = $sp->fetch();
        	$data->closeCursor();  
        	*/
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
        	$retVal = 0;
        	
        	if (!empty($title) && !empty($post))
        	{
        		$mysqldate = date('Y-m-d H:i:s');
				
        		$success = $db->insert("blog", array("title"=>$title,"post"=>$post,"active"=>$active,"created"=>$mysqldate));
        		if ($success)
        		{
        			$retVal = $db->lastInsertId();
        		}
        		else
        		{
        			$retVal = -2;
        		}
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
    
    public function setEditBlogPost($idList=null, $active=0) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spSetUpdateBlogPosts(?,?,@retVal)");
        	$data->bindParam(1, $idList);
        	$data->bindParam(2, $active);
        	$data->execute();
        	//Get the return value: 1: Success, 2: Success with no update
			$sp = $this->getDefaultAdapter()->prepare("SELECT @retVal as retVal");
			$sp->execute();
			$result = $sp->fetch();
        	$data->closeCursor();  
        	*/
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (!empty($idList))
        	{
        		$SQL .= "UPDATE blog SET active = ".$active." WHERE FIND_IN_SET(id, '".$idList."')";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	$retVal = 1;
            }
            
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
    
    public function setDeletePosts($idList=null) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (!empty($idList))
        	{
        		$SQL .= "DELETE FROM blog WHERE FIND_IN_SET(id, '".$idList."')";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	$retVal = 1;
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    }
    
    public function setDeletePost($id) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (!empty($id)&&is_numeric($id))
        	{
        		$SQL .= "DELETE FROM blog WHERE id='".$id."'";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	$retVal = 1;
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    }
    
    public function setEditArticle($postid,$title,$post,$active) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spSetUpdateArticle(?,?,?,?,@retVal)");
        	$data->bindParam(1, $postid);
        	$data->bindParam(2, $title);
        	$data->bindParam(3, $post);
        	$data->bindParam(4, $active);
        	$data->execute();
        	//Get the return value: < 0: Success, 0: Already Exists, -1: Invalid Inputs
			$sp = $this->getDefaultAdapter()->prepare("SELECT @retVal as ID");
			$sp->execute();
			$result = $sp->fetch();
        	$data->closeCursor();  
        	*/

			$db = Zend_Db_Table::getDefaultAdapter();
        	$retVal = 0;
        	
        	if (!empty($title) && !empty($post) && isset($postid) && is_numeric($postid) && $postid > 0)
        	{
        		$mysqldate = date('Y-m-d H:i:s');
        	
        		$success = $db->update("blog", array("title"=>$title,"post"=>$post,"active"=>$active,"modified"=>$mysqldate), $db->quoteInto(" id = ? " ,$postid));
        		if ($success == 1 || $success == 0)
        		{
        			$retVal = $postid;
        		}
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
}