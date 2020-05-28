<?php
/**
 *
 * Hide url for guests. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, dmzx, https://www.dmzx-web.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace dmzx\hideurlforguests\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\language\language;
use phpbb\user;

class listener implements EventSubscriberInterface
{
	/** @var language */
	protected $language;

	/** @var user */
	protected $user;

	/**
	* Constructor
	*
	* @param language			$language
	* @param user				$user
	*/
	public function __construct(
		language $language,
		user $user
	)
	{
		$this->language 	= $language;
		$this->user 		= $user;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.viewtopic_post_rowset_data' => 'viewtopic_post_rowset_data',
		];
	}

	public function viewtopic_post_rowset_data($event)
	{
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			$row = $event['rowset_data'];

			$this->language->add_lang('common', 'dmzx/hideurlforguests');

			foreach($this->url_regex() as $regex)
			{
				$row['post_text'] = preg_replace($regex, $this->language->lang('HURLFG_TEXT'), $row['post_text']);
			}
			$event['rowset_data'] = $row;
		}
	}

	private function url_regex()
	{
		$url_regex = [
			'~<a[^>]*>.*?</a>~i',
			'~<url[^>]*>.*?</url>~i',
			'~\[url[^>]*?\].*?\[/url[^>]*?\]~i'
		];

		return $url_regex;
	}
}
