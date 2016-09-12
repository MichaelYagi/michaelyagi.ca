<?php

class FeedController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->headTitle(':Feed');
    }

    public function indexAction()
    {
    	$site = new Application_Model_SiteOptions;
    	$sitestatus = $site->getSiteStatus();
    
    	$blog = new Application_Model_Blog();
    	$arrBlog = $blog->getBlogPosts();
		$baseUrl = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHttpHost();

		$feedArr = array('title' => 'News',
                     'link'  => $baseUrl.'/feed',
                     'description' => 'Recent posts',
                     'language' => 'en-ca',
                     'charset' => 'utf-8',
                     'generator' => 'michaelyagi.ca',
                     'entries' => array()
              );
		
		//If site is down
		if(!$sitestatus)
		{
			$feedArr['entries'][] = array('title' => 'The website is down',
       	                               'link' => $baseUrl,
       	                               'description' => 'The website is down, please stay tuned!',
       	                               'publishdate' => date('Y-m-d H:i:s'),
       	                               'guid' => $baseUrl
       	                               );
		}
		else
		{
			$counter = 0;
			foreach($arrBlog as $value)
			{		
				if ($counter == 15)
				{
					break;
				}
				$feedArr['entries'][] = array('title' => $value['title'],
       	                               'link' => $baseUrl.'/news/article/id/'.$value['id'],
       	                               'description' => $value['post'],
       	                               'publishdate' => $value['created'],
       	                               'guid' => $baseUrl.'/news/article/id/'.$value['id']
       	                               );
				$counter++;
			}
		}
		$feed = Zend_Feed::importArray($feedArr, 'rss');
		echo $feed->send(); exit();
     }
	
}