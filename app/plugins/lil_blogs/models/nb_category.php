<?php
/* SVN FILE: $Id: nb_category.php 126 2009-07-02 07:21:52Z miha@nahtigal.com $ */
/**
 * Short description for nb_category.php
 *
 * Long description for nb_category.php
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2009, Miha Nahtigal
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2009, Miha Nahtigal
 * @link          http://www.nahtigal.com/
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 * @since         v 1.0
 * @version       $Revision: 126 $
 * @modifiedby    $LastChangedBy: miha@nahtigal.com $
 * @lastmodified  $Date: 2009-07-02 09:21:52 +0200 (čet, 02 jul 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * NbCategory class
 *
 * @uses          LilBlogsAppModel
 * @package       lil_blogs
 * @subpackage    lil_blogs.models
 */
class NbCategory extends LilBlogsAppModel {
/**
 * name property
 *
 * @var string 'NbCategory'
 * @access public
 */
	var $name = 'NbCategory';
/**
 * name cacheQueries
 *
 * @var boolean
 * @access public
 */
	var $cacheQueries = false;
/**
 * getCategories method
 *
 * Get the list of categories with basic data.
 *
 * @access public
 * @return array key = category ids, values = array(keys = 'probability', 'word_count')
 */
	function getCategories() {
		$categories = $this->find('all');
		$result = array();
		foreach ($categories as $category) {
			$result[$category['NbCategory']['id']] = array(
				'probability' => $category['NbCategory']['probability'],
				'word_count'  => $category['NbCategory']['word_count']
			);
		}
		return $result;
	}
/**
 * wordExists method
 *
 * See if the word is an already learnt word.
 *
 * @param string $word
 * @access public
 * @return bool
 */
	function wordExists($word) {
		App::import('Model', 'LilBlogs.NbWordfreq'); $this->NbWordfreq = new NbWordfreq();
		return $this->NbWordfreq->findCount(array('word'=>$word))>0;
	}
/**
 * getWord method
 *
 * Get details of a word in a category.
 *
 * @param string $word
 * @param string $category_id
 * @access public
 * @return array ('count' => count)
 */
	function getWord($word, $category_id) {
		App::import('Model', 'LilBlogs.NbWordfreq'); $this->NbWordfreq = new NbWordfreq();
		$result = array('count'=>0);
		if ($count = $this->NbWordfreq->field('count', array('word'=>$word, 'category_id'=>$category_id))) {
			$result['count'] = $count;
		}
		return $result;
	}
/**
 * updateWord method
 *
 * Update a word in a category.
 * If the word is new in this category it is added, else only the count is updated.
 *
 * @param int $count
 * @param string $category_id
 * @access public
 * @return bool Success
 */
	function updateWord($word, $count, $category_id) {
		App::import('Model', 'LilBlogs.NbWordfreq'); $this->NbWordfreq = new NbWordfreq();
		$oldword = $this->getWord($word, $category_id);
		if (0 == $oldword['count']) {
			$this->NbWordfreq->create();
		}
		return $this->NbWordfreq->save(array(
			'id' => $this->NbWordfreq->field('id', array('word'=>$word, 'category_id'=>$category_id)),
			'word' => $word,
			'category_id' => $category_id,
			'count' => $count+$oldword['count']
		));
	}
/**
 * removeWord method
 *
 * Remove a word from a category.
 *
 * @param string $word
 * @param int $count
 * @param string $category_id 
 * @access public
 * @return bool Success
 */
	function removeWord($word, $count, $category_id) {
		App::import('Model', 'LilBlogs.NbWordfreq'); $this->NbWordfreq = new NbWordfreq();
		$oldword = $this->getWord($word, $category_id);
		if (0 != $oldword['count'] && 0 >= ($oldword['count']-$count)) {
			return $this->NbWordfreq->deleteAll(array('word'=>$word, 'category_id'=>$category_id));
		} else {
			return $this->NbWordfreq->save(array(
				'id' => $this->NbWordfreq->field('id', array('word'=>$word, 'category_id'=>$category_id)),
				'word' => $word,
				'category_id' => $category_id,
				'count' => $count
			));
		}
	}
/**
 * updateProbabilities method
 *
 * Update the probabilities of the categories and word count.
 * This function must be run after a set of training.
 *
 * @access public
 * @return bool Success
 */
	function updateProbabilities() {
		App::import('Model', 'LilBlogs.NbWordfreq'); $this->NbWordfreq = new NbWordfreq();
		$freqTable = $this->NbWordfreq->table;
		if (!empty($this->NbWordfreq->tablePrefix)) $freqTable = $this->NbWordfreq->tablePrefix.$freqTable;
    	// first update the word count of each category
   		$data = $this->query("SELECT category_id, SUM(count) AS total FROM ".$freqTable." GROUP BY category_id", false);
        $total_words = 0;
        foreach ($data as $f) {
            $total_words += $f[0]['total'];
        }

        if ($total_words == 0) {
            $this->updateAll(array('word_count'=>0, 'probability'=>0));
            return true;
        }
        
        foreach ($data as $f) {
            $proba = $f[0]['total']/$total_words;
            $this->updateAll(array('word_count'=>$f[0]['total'], 'probability'=>$proba), array('id'=>$f[$freqTable]['category_id']));
        }
        return true;
	}
/**
 * saveReference method
 *
 * Save a reference in the database.
 *
 * @param string $doc_id Unique reference
 * @param string $category_id
 * @param string $content Content of the reference
 * @access public
 * @return mixed
 */
	function saveReference($doc_id, $category_id, $content) {
		App::import('Model', 'LilBlogs.NbReference'); $this->NbReference = new NbReference();
		$this->NbReference->create();
		return $this->NbReference->save(array(
			'id' => $doc_id,
			'category_id' => $category_id, 
			'content' => $content
		));
	}
/**
 * getReference method
 *
 * Get a reference from the database.
 *
 * @param string $doc_id Reference id.
 * @access public
 * @return string Reference id.
 */
	function getReference($doc_id) {
		App::import('Model', 'LilBlogs.NbReference'); $this->NbReference = new NbReference();
		$result = array();
		if ($data = $this->NbReference->findById($doc_id)) {
			$result['category_id'] = $data['NbReference']['category_id'];
			$result['content'] = $data['NbReference']['content'];
			$result['id'] = $data['NbReference']['id'];
		}
		return $result;
	}
/**
 * removeReference method
 *
 * Remove a reference from the database.
 *
 * @param string $doc_id Reference id.
 * @access public
 * @return bool Success.
 */
	function removeReference($doc_id) {
		App::import('Model', 'LilBlogs.NbReference'); $this->NbReference = new NbReference();
		return $this->NbReference->deleteAll(array('id'=>$doc_id));
	}
}
?>
