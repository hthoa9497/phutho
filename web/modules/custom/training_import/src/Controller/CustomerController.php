<?php

namespace Drupal\training_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomerController
 ** @package Drupal\training_import\Controller
 */
class CustomerController extends ControllerBase {
  protected $database;

  /**
   * Constructs a new CustomerController
   */
  public function __construct($database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function overview() {
    $header = [
      'nid' => [
        'data'  => $this->t('ID'), 
        'field' => 'nid',
        'specifier'  => 'nid'
        // 'sort'  => 'desc'
      ],
      'name' => [
        'data'      => $this->t('Name'),
        'field'     => 'title',
        'specifier' => 'title'
      ],
      'email' => [
        'data'      => $this->t('Email'),
        'field'     => 'field_email',
        'specifier' => 'field_email'
      ],
      'phone' => [
        'data'      => $this->t('Phone'),
        'field'     => 'field_phone',
        'specifier' => 'field_phone'
      ],
      'birthday' => [
        'data'      => $this->t('Birthday'),
        'field'     => 'field_birthday',
        'specifier' => 'field_birthday'
      ],
      'address' => [
        'data'      => $this->t('Address'),
        'field'     => 'field_address',
        'specifier' => 'field_address'
      ]
    ];

    $nids = $this->entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'customers')
      ->pager(20)
      ->tableSort($header)
      ->execute();

    $customer_info = [];
    foreach ($nids as $nid) {
      $node = Node::load($nid);
      $customer_info[] = [
        $node->id(),
        $node->label(),
        $node->{'field_email'}->getString(),
        $node->{'field_phone'}->getString(),
        $node->{'field_birthday'}->getString(),
        $node->{'field_address'}->getString(),
      ];
    }

    return [
      'results' => [
        '#type'    => 'table',
        '#caption' => $this->t('Customer Table'),
        '#header'  => $header,
        '#rows'    => $customer_info,
        '#empty'   => $this->t('No customer found.'),
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

  public function overviewAjaxCallback(\Symfony\Component\HttpFoundation\Request $request) {
    $header = [
      'nid' => [
        'data'  => $this->t('ID'), 
        'field' => 'nid',
        'specifier'  => 'nid'
      ],
      'name' => [
        'data'      => $this->t('Name'),
        'field'     => 'title',
      ],
      'email' => [
        'data'      => $this->t('Email'),
        'field'     => 'field_email',
        'specifier' => 'field_email'
      ],
      'phone' => [
        'data'      => $this->t('Phone'),
        'field'     => 'field_phone',
        'specifier' => 'field_phone'
      ],
      'birthday' => [
        'data'      => $this->t('Birthday'),
        'field'     => 'field_birthday',
        'specifier' => 'field_birthday'
      ],
      'address' => [
        'data'      => $this->t('Address'),
        'field'     => 'field_address',
        'specifier' => 'field_address'
      ]
    ];

    // $size = $form_state->getValue('customer_preview_size', 1);
    $form_raw = $request->request->all();
    $size = $form_raw['customer_preview_size'];
  
    $nids = $this->entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'customers')
      ->range(0, $size)
      ->tableSort($header)
      ->execute();

    $customer_info = [];
    foreach ($nids as $nid) {
      $node = Node::load($nid);
      $customer_info[] = [
        $node->id(),
        $node->label(),
        $node->{'field_email'}->getString(),
        $node->{'field_phone'}->getString(),
        $node->{'field_birthday'}->getString(),
        $node->{'field_address'}->getString(),
      ];
    }

    $results = [
      'status' => [
        '#type' => 'status_messages'
      ],
      'text' => [
        '#type' => 'textarea',
        '#title' => 'textt',
        '#value' => print_r($form_raw, true),
      ],
      'result' => [
        '#type'    => 'table',
        // '#caption' => $this->t('Customer Table'),
        '#header'  => $header,
        '#rows'    => $customer_info,
        '#empty'   => $this->t('No customer found.'),
      ],
      'pager' => [
        '#type' => 'pager',
      ],
      '#attached' => [
        'library' => [
          'core/drupal.dialog.ajax',
          'views/views.ajax'
        ]
      ]
    ];

    $response = new \Drupal\Core\Ajax\AjaxResponse;
    $response->addCommand(new \Drupal\Core\Ajax\OpenModalDialogCommand(
      $this->t('Customer Preview'),
        $results,
        [
          'width' => '1000',
          'resizable' => TRUE,
          'draggable' => TRUE
        ])
      );
    return $response;
  }
}
