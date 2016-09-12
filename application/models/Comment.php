<?php

class Application_Model_Comment extends Zend_Db_Table_Abstract 
{ 

	public function getBlogPostComments($blogid=null, $activeonly=1, $order="post") 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	//No parameters to bind
        	$data = $this->getDefaultAdapter()->prepare("CALL spGetComments(?,?,?)");
        	$data->bindParam(1, $blogid);
        	$data->bindParam(2, $activeonly);
        	$data->bindParam(3, $order);
        	$data->execute();
        	//fetchAll into an array
        	$result = $data->fetchAll();
        	$data->closeCursor();
        	*/
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (isset($blogid) && is_numeric($blogid) && $blogid > 0)
        	{
        		if ($activeonly == 1)
        		{
        			$SQL .= 'SELECT c.id as id, c.name as name, c.email as email, c.comment as comment, c.created as created, c.active as isActive, c.markedread as markedRead FROM comment c WHERE c.active = 1 AND c.blogid = '.$blogid.' ORDER BY c.id DESC';
        		}
        		else
      		  	{
        			$SQL .= 'SELECT c.id as id, c.name as name, c.email as email, c.comment as comment, c.created as created, c.active as isActive, c.markedread as markedRead FROM comment c WHERE c.blogid = '.$blogid.' ORDER BY ';
        			if ($order == 'post')
        			{
        				$SQL .= 'c.id DESC';
        			}
        			else if ($order == 'status')
        			{
        				$SQL .= 'c.active';
        			}
        			else if ($order == 'blogid')
        			{
        				$SQL .= 'c.blogid';
        			}
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
    
    public function getAllComments($order="unread") 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	//No parameters to bind
        	$data = $this->getDefaultAdapter()->prepare("CALL spGetAllComments(?)");
        	$data->bindParam(1, $order);
        	$data->execute();
        	//fetchAll into an array
        	$result = $data->fetchAll();
        	$data->closeCursor();
        	*/
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = 'SELECT c.id as id, c.blogid as blogid, c.name as name, c.email as email, c.comment as comment, c.created as created, c.active as isActive, c.markedread as markedRead FROM comment c ORDER BY ';
        	
        	if ($order == 'post')
        	{
        		$SQL .= 'c.id DESC';
        	}
        	else if ($order == 'status')
        	{
        		$SQL .= 'c.active ASC';
        	}
        	else if ($order == 'unread')
        	{
        		$SQL .= 'c.markedread ASC';
        	}
        	else if ($order == 'articleid')
        	{
        		$SQL .= 'c.blogid DESC';
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
    
    public function getComment($id=null) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
        	$SQL = '';
        	
        	if (isset($id) && is_numeric($id) && $id > 0)
        	{
				$SQL .= 'SELECT c.id as id, c.blogid as blogid, c.name as name, c.email as email, c.comment as comment, c.created as created, c.active as isActive, c.markedread as markedRead FROM comment c WHERE c.id='.$id;
        	}
        	else
        	{
        		$retVal = -1;
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
    
    public function setNewComment($blogid=null, $cname = null, $cemail = null, $comment = null, $active=1) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spSetNewComment(?,?,?,?,?,@retVal)");
        	$data->bindParam(1, $blogid);
        	$data->bindParam(2, $cname);
        	$data->bindParam(3, $cemail);
        	$data->bindParam(4, $comment);
        	$data->bindParam(5, $active);
        	$data->execute();
			$sp = $this->getDefaultAdapter()->prepare("SELECT @retVal as ID");
			$sp->execute();
			$result = $sp->fetch();
        	$data->closeCursor(); 
        	*/
        	
        	$db = Zend_Db_Table::getDefaultAdapter();
        	
        	if (!empty($cname) && !empty($cemail) && !empty($comment) && isset($blogid) && is_numeric($blogid) && $blogid > 0)
        	{
        		$mysqldate = date('Y-m-d H:i:s');
        		$success = $db->insert("comment", array("blogid"=>$blogid,"name"=>$cname,"email"=>$cemail,"comment"=>$comment,"active"=>$active,"created"=>$mysqldate));
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
    
    public function setEditComments($idList=null, $active=0) 
    {              
        try 
        {   
        	/*
        	//Call stored procedure with pdo set in application config file
        	$data = $this->getDefaultAdapter()->prepare("CALL spSetUpdateComments(?,?,@retVal)");
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
        		$SQL .= "UPDATE comment SET active = ".$active." WHERE FIND_IN_SET(id, '".$idList."')";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	if ($result != false)
           		{
           			$retVal = 1;
           		}
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    }
    
    public function setDeleteComments($idList=null, $active=0) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (!empty($idList))
        	{
        		$SQL .= "DELETE FROM comment WHERE FIND_IN_SET(id, '".$idList."')";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	if ($result != false)
           		{
           			$retVal = 1;
           		}
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    }
    
    public function setDeleteComment($id) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (!empty($id)&&is_numeric($id))
        	{
        		$SQL .= "DELETE FROM comment WHERE id='".$id."'";
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	if ($result != false)
           		{
           			$retVal = 1;
           		}
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    }
    
    public function setEditComment($id=null, $active=0) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (isset($id) && is_numeric($id) && $id > 0)
        	{
        		$SQL .= "UPDATE comment SET active = ".$active." WHERE id = ".$id;
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	if ($result != false)
            	{
            		$retVal = 1;
            	}
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
    
    public function setCommentRead($id=null) 
    {              
        try 
        {   
        	$db = Zend_Db_Table::getDefaultAdapter();
			$SQL = '';
        	
        	if (isset($id) && is_numeric($id) && $id > 0)
        	{
        		$SQL .= "UPDATE comment SET markedread = 1 WHERE id = ".$id;
        	}
        	else
        	{
        		$retVal = -1;
        	}
               
            if(!empty($SQL))
        	{
            	$result = $db->query($SQL);   
            	if ($result != false)
            	{
            		$retVal = 1;
            	}
            }
               
        } catch (Exception $ex) { 
                throw $ex; 
        }       
        
        return $retVal;
    } 
    
    public function setMarkComments($idList=null, $active=0) 
    {   
        $db = Zend_Db_Table::getDefaultAdapter();
		$SQL = '';
        	
        if (!empty($idList))
        {
        	$SQL .= "UPDATE comment SET markedread = ".$active." WHERE FIND_IN_SET(id, '".$idList."')";
        }
        else
        {
        	$retVal = -1;
        }
               
        if(!empty($SQL))
        {
           	$result = $db->query($SQL);   
           	if ($result != false)
           	{
           		$retVal = 1;
           	}
        }
        return $retVal;      
    } 
    
}