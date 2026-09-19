<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Task.updatenotification
 *
 * @copyright   (C) 2023 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KWP121\Plugin\Task\SendNewsworld\Extension;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Mail\Exception\MailDisabledException;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Asset;
use Joomla\CMS\Factory;

//use Joomla\CMS\Updater\Updater;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\SubscriberInterface;
use PHPMailer\PHPMailer\Exception as phpMailerException;
use Joomla\CMS\User\UserHelper;

\defined('_JEXEC') or die;

/**
 * A task plugin. Offers 2 task routines Invalidate Expired Consents and Remind Expired Consents
 * {@see ExecuteTaskEvent}.
 *
 * @since 5.0.0
 */
final class SendNewsworld extends CMSPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;
    use TaskPluginTrait;

    /**
     * @var string[]
     * @since 5.0.0
     */
    private const TASKS_MAP = [
        'send.newsworld' => [
            'langConstPrefix' => 'PLG_TASK_SENDNEWSWORLD_SEND',
            'method'          => 'sendNews',
            'form'            => 'sendForm',
        ],
    ];

    /**
     * @var boolean
     * @since 5.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * @inheritDoc
     *
     * @return string[]
     *
     * @since 5.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    /**
     * Method to send the update notification.
     *
     * @param   ExecuteTaskEvent  $event  The `onExecuteTask` event.
     *
     * @return integer  The routine exit code.
     *
     * @since  5.0.0
     * @throws \Exception
     */
    private function sendNews(ExecuteTaskEvent $event): int
    {
        $specificEmail  = $event->getArgument('params')->email ?? '';
        $forcedLanguage = $event->getArgument('params')->language_override ?? '';
		$groups = $event->getArgument('params')->usergroup ?? '';

		$chosen =(int) $this->params->get("newsworldid");
		
		$db = Factory::getDbo();
	
     
        $eid = ExtensionHelper::getExtensionRecord('joomla', 'file')->extension_id;

     

        $model = $this->getApplication()->bootComponent('com_newsworld')
            ->getMVCFactory()->createModel('Newsworlds', 'Administrator', ['ignore_request' => true]);

        $model->setState('filter.extension_id', $eid);
		
        $newsitems = $model->getItems();
        if (empty($newsitems)) {
            return Status::OK;			
        }
		$news = [];
		foreach($newsitems as $i=>$item):
		    if($item->published)
				$news[] = $item;
		endforeach;			


		if(empty($chosen))
		  $newsworld = end($news);
		else
			$newsworld = $news[--$chosen];
		
		 $nw = $this->getApplication()->bootComponent('com_newsworld')
            ->getMVCFactory()->createModel('Newsworld', 'Administrator', ['ignore_request' => true]);
			$sendnews = $nw->getItem($newsworld->id);		
		
		
		  $usersmodel = $this->getApplication()->bootComponent('com_users')
            ->getMVCFactory()->createModel('Users', 'Administrator', ['ignore_request' => true]);
			
			$users = $usersmodel->getItems();
			 
						  

		
			$filteredusers = [];
			
			foreach($users as $i=>$user)
			{
				if($user->block==0)
				{
					
					$usergroups = UserHelper::getUserGroups($user->id);
				
					
					foreach($usergroups as $i=>$id):	
							if(in_array($id, $groups))
								$filteredusers[] = $user;
						endforeach;
						
						

				}
			}
		
			
			        $sitename = $this->getApplication()->get('sitename');
			
			 $jLanguage = $this->getApplication()->getLanguage();
        $jLanguage->load('plg_task_newsworld', JPATH_ADMINISTRATOR, 'en-GB', true, true);
        $jLanguage->load('plg_task_newsworld', JPATH_ADMINISTRATOR, null, true, false);
		
		  if (!empty($forcedLanguage)) {
            $jLanguage->load('plg_task_newsworld', JPATH_ADMINISTRATOR, $forcedLanguage, true, false);
        }
		
	

		
		  $baseURL  = Uri::base();
        $baseURL  = rtrim($baseURL, '/');
        $baseURL .= (!str_ends_with($baseURL, 'administrator')) ? '/administrator/' : '/';
       $baseURL .= 'index.php?option=com_newsworld';
        $uri      = new Uri($baseURL); 		
		    $substitutions = [         
           'sitename' => $sitename,
           'news' => $sendnews->description,
        ];

     $mailer = Factory::getMailer();
			
			foreach($filteredusers as $fu=>$user):
			
			     
           try {
                $mailer->addRecipient($user->email);
			   $mailer->setSubject("Real World News:".$newsworld->title);
			   $mailer->isHtml(true);
              $mailer->Encoding = 'base64';
              $mailer->setBody($sendnews->description);
                $mailer->send();
				
            } 
			catch ( MailDisabledException | phpMailerException $exception ) {
                try {
				
                    $this->logTask($jLanguage->_($exception->getMessage()." userid:".$user->id." username:".$user->username. " email:".$user->email));
				   
				  

					
                } catch (\RuntimeException) {
                    return Status::KNOCKOUT;
                }
            }
			
			    
			endforeach;
			
			
			

        $this->logTask($this->getApplication()->getLanguage()->_('PLG_TASK_SENDNEWSWORLD_SEND_END'), 'info'); 

        return Status::OK;
    }

}