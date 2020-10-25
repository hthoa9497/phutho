<?php
/**
 * @file
 * Contains \Drupal\training_import\Plugin\QueueWorker\TrainingImportWorker
 */
namespace Drupal\training_import\Plugin\QueueWorker;

use Drupal\Core\Database\Connection;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * @QueueWorker(
 *   id = "training_import_worker",
 *   title = @Translation("Training Import Worker"),
 *   cron = {"time" = 60}
 * )
 */
class TrainingImportWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {
  /**
   * Contains the configuration object factory.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
  /**
   * The *Watchdog* logger for Training Import module
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs an EventsDataImportWorker
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, Connection $database, LoggerChannelFactoryInterface $logger_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->logger = $logger_factory->get('Training:Import');
  }

  /**
   * {@inheritdoc}
   */
  public static function create($container, $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $block_id   = $data['block_id'];
    $block_size = $data['block_size'];

    $this->logger
      ->info(t('Queue processed block @block_id', [
          '@block_id' => $block_id
        ]
      )
    );

    try {
      $rows = $this->database
        ->select('training_import_data', 't')
        ->fields('t')
        ->condition('type', 'customers')
        ->range(0, $block_size)
        ->execute();

      foreach ($rows as $row) {
        $data = unserialize($row->data);

        // $this->logger
        //   ->warning(print_r($data, true));
        
        $direct_map = [
          'field_first_name',
          'field_last_name',
          'field_email',
          'field_phone',
          'field_address',
          // 'field_birthday' => 'Birthday',
          // 'field_position' => 'Position',
          // 'field_gender'   => 'Gender',
          // 'field_company'  => 'Company name'
        ];

        $node = Node::create([
          'type'  => 'customers',
          'title' => 'Untitled'
        ]);

        foreach ($direct_map as $field_name) {
          $node->set($field_name, $data[$field_name]);
        }

        $node->set('title', $data['field_first_name'] . ' ' . $data['field_last_name']);

        $node->save();
      }

      $rids = $this->database
        ->select('training_import_data', 't')
        ->fields('t', ['rid', 'type'])
        ->condition('type', 'customers')
        ->range(0, $block_size)
        ->execute()
        ->fetchCol(0);

      // $this->logger
      //   ->warning(print_r($rids, true));
      
      if (!empty($rids)) {
        $this->database
          ->delete('training_import_data')
          ->condition('rid', $rids, 'IN')
          ->execute();
      }
    }
    catch (\Exception $e) {
      $this->logger
        ->error(t('An error occured while importing: @msg', [
          '@msg' => $e->getMessage()
        ])
      );
      throw $e;
    }
  }
}
