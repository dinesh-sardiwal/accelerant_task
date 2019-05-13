<?php

namespace Drupal\accelerant_task\Controller;

use Drupal\Component\Utility\Html;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for accelerant_task module routes.
 */
class AxelerantController {

  /**
   * Returns node details if key matches and node of type 'page' exists.
   *
   * @param string $siteapikey
   *   The site api key.
   * @param int $nid
   *   The node ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing node details.
   */
  public function page_json($siteapikey, $nid) {
    $key = \Drupal::config('system.site')->get('siteapikey');
    $flag = 0;
    if ($siteapikey != $key) {
      $flag = 1;
    }
    if ($flag == 0) {
      $data = \Drupal::entityQuery('node')
        ->condition('type', 'page')
        ->condition('nid', $nid)
        ->execute();
      if (empty($data)) {
        $flag = 1;
      }
    }
    if ($flag == 1) {
      $response['data'] = 'Access denied';
    }
    else {
      $node = Node::load($nid);
      $response['data'][] = [
        'nid' => $node->get('nid')->value,
        'type' => $node->get('type')->target_id,
        'title' => $node->get('title')->value,
        'content' => check_markup($node->get('body')->value, $node->get('body')->format),
      ];
    }
    return new JsonResponse($response);
  }
}
