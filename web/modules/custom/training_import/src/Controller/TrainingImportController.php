<?php

namespace Drupal\training_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Queue\QueueFactory;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

use Drupal\training_import\TrainingUtils;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 ** Class TrainingImportController.
 ** @package Drupal\training_import\Controller
 */
class TrainingImportController extends ControllerBase {
  /**
   * This is a missing trait of core's ControllerBase that
   * causes error when injecting database connection,
   * @see https://www.drupal.org/project/drupal/issues/2893029
   */
  use DependencySerializationTrait;

  /**
   * The current database connection
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
  /**
   * @var \Drupal\Core\StreamWrapper\StreamWrapperInterface
   */
  protected $streamWrapperManager;
  /**
   * The *Watchdog* logger for Training Import module
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Queue\QueueInterface 
   */
  protected $queue;

  /**
   * Constructs an TrainingImportController object
   * 
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger service.
   */
  public function __construct(
    Connection $database,
    StreamWrapperManagerInterface $stream_wrapper_manager,
    LoggerChannelFactoryInterface $logger_factory,
    QueueFactory $queue_factory)
  {
    $this->database = $database;
    $this->streamWrapperManager = $stream_wrapper_manager;

    $this->logger = $logger_factory->get('Training:Import');
    $this->queue  = $queue_factory->get('training_import_worker');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('stream_wrapper_manager'),
      $container->get('logger.factory'),
      $container->get('queue')
    );
  }

  /**
   * The constants to use while verifying spreadsheet
   */
  const SHEET_VERIFY_SUCCESS = 0;
  const SHEET_VERIFY_FAILED  = 1;

  /**
   * @param int $file_id The fid of the import file
   * @return mixed Spreadsheet on success, NULL on failure
   */
  public function getSheetFromFileID($file_id) {
    try {
      $file = File::load($file_id);
      $file_path = $this->streamWrapperManager
        ->getViaUri($file->getFileUri())
        ->realpath();

      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
    }
    catch (\Exception $e) {
      $this->messenger()
        ->addError($e->getMessage());
      return NULL;
    }
    return $spreadsheet;
  }

  /**
   * @return string The raw data from spreadsheet
   */
  private static function getRowDataByFieldName($row_data, $column_map, $sheet_key) {
    return $row_data[$column_map[$sheet_key]];
  }

  /**
   * The import function assumes the file is verified,
   * hence no check is perfomed here
   * @param int $file_id The fid of the import file
   * @return string the result message
   */
  public static function import($file_id) {
    $spreadsheet = self::getSheetFromFileID($file_id);

    # First import the Companies data
    $company_data = $spreadsheet->getSheetByName('Companies')->toArray(null, true, true, true);
    // The first row should contains table's column titles
    $column_map =  array_flip($company_data[1]);

    $key_maps = [
      'Company name' => 'field_company_name',
      'Email'   => 'field_email',
      'Phone'   => 'field_phone',
      'Website' => 'field_website',
      'Address' => 'field_location',
    ];

    foreach ($company_data as $row_id => $row_data) {
      if ($row_id > 1) {
        $node = Node::create([
          'type'  => 'company',
          'title' => 'Untitled'
        ]);

        foreach ($key_maps as $sheet_key => $field_key) {
          $data = self::getRowDataByFieldName($row_data, $column_map, $sheet_key);
          $node->set($field_key, $data);
        }

        $company_name = $node->{'field_company_name'};
        $company_name = $company_name ? $node->{'field_company_name'}->getString() : '';
        if ($company_name != '') {
          $node->set('title', $company_name);
        }

        $url = self::getRowDataByFieldName($row_data, $column_map, 'Website');
        if (!(strpos('http://', $url) || strpos('https://', $url))) {
          $url = 'http://' . $url;
        }
        $node->set('field_website', [
          'uri'   => $url,
          'title' => $company_name
        ]);
        $node->save();
      }
    }

    # Then the Customer data
    $customer_data = $spreadsheet->getSheetByName('Customers')->toArray(null, true, true, true);

    // The first row should contains table's column titles
    $column_map =  array_flip($customer_data[1]);

    $key_maps = [
      'First name' => 'field_first_name',
      'Last name'  => 'field_last_name',
      'Email'   => 'field_email',
      'Phone'   => 'field_phone',
      'Address' => 'field_address',
    ];

    foreach ($customer_data as $row_id => $row_data) {
      if ($row_id > 1) {
        $node = Node::create([
          'type'  => 'customers',
          'title' => 'Untitled'
        ]);

        foreach ($key_maps as $sheet_key => $field_key) {
          $data = self::getRowDataByFieldName($row_data, $column_map, $sheet_key);
          $node->set($field_key, $data);
        }

        $customer_name =
          self::getRowDataByFieldName($row_data, $column_map, 'First name')
          . ' ' . self::getRowDataByFieldName($row_data, $column_map, 'Last name');
        if ($company_name != '') {
          $node->set('title', $customer_name);
        }
        
        $birthday = self::getRowDataByFieldName($row_data, $column_map, 'Birthday');
        $node->set('field_birthday', [
          TrainingUtils::convertDateFormat($birthday, ['m-d-Y', 'm/d/Y'])
        ]);

        $position = self::getRowDataByFieldName($row_data, $column_map, 'Position');
        if ($tid = TrainingUtils::getTermIDByName($position, 'position')) {
          $node->{'field_position'} = Term::load($tid);
        }

        $gender = self::getRowDataByFieldName($row_data, $column_map, 'Gender');
        if ($tid = TrainingUtils::getTermIDByName($gender, 'gender')) {
          $node->{'field_gender'} = Term::load($tid);
        }

        $company_name = self::getRowDataByFieldName($row_data, $column_map, 'Company name');
        $company_nids = \Drupal::entityQuery('node')
          ->condition('type', 'company')
          ->condition('field_company_name', $company_name)
          ->execute();
    
        if ($company_nids) {
          $node->{'field_company'} = Node::load(reset($company_nids));
        }

        $node->save();
      }
    }
    return true;
  }

  /**
   * @param int $file_id The fid of the spreadsheet file to verify
   * @return array An array represent the verify result
   */
  public static function verifySpreadsheet($file_id) {
    $spreadsheet = self::getSheetFromFileID($file_id);

    $return_code    = TrainingImportController::SHEET_VERIFY_SUCCESS;
    $error_messages = [];

    if ($spreadsheet->sheetNameExists('Customers') == FALSE) {
      $return_code = TrainingImportController::SHEET_VERIFY_FAILED;
      $error_messages[] = 'The "Customer" sheet is not found!';
    }
    if ($spreadsheet->sheetNameExists('Companies') == FALSE) {
      $return_code = TrainingImportController::SHEET_VERIFY_FAILED;
      $error_messages[] = 'The "Companies" sheet is not found!';
    }

    // $sheet_data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    // print_r($sheet_data);exit;

    return [
      'result_code'    => $return_code,
      'error_messages' => $error_messages
    ];
  }

  public function doBatchImport($file_id) {
    $batch = [
      'title' => $this->t('Processing spreadsheet'),
      'init_message'  => $this->t('Start Importing'),
      'error_message' => $this->t('An error occurred while importing'),
      'operations' => [
        [
          [$this, 'doProcessBatch'],
          [
            $file_id,
            200
          ],
        ],
        [
          [$this, 'doImportBatch'],
          [
            100
          ],
        ],
      ],
      'finished' => [$this, 'doFinishBatch'],
    ];
    batch_set($batch);
  }

  /**
   * @param int $file_id ID of the spreadsheet file to import
   * @param int $process_limit The rows to process each batch
   */
  public function doProcessBatch($file_id, int $process_limit, &$context) {
    $sandbox = &$context['sandbox'];

    if (empty($sandbox)) {
      $spreadsheet = $this->getSheetFromFileID($file_id);
      $raw_data    = $spreadsheet
        ->getSheetByName('Customers')
        ->toArray(null, true, true, true);

      $sandbox = [
        'raw_data' => $raw_data,
        // Real data start from row 2
        'progress' => 2,
        'total'    => count($raw_data)
      ];
      $context['results']['queued'] = 0;
    }

    $customer_data = $sandbox['raw_data'];
    $column_map =  array_flip($customer_data[1]);

    $key_maps = [
      'field_first_name' => 'First name',
      'field_last_name'  => 'Last name',
      'field_email'    => 'Email',
      'field_phone'    => 'Phone',
      'field_address'  => 'Address',
      'field_birthday' => 'Birthday',
      'field_position' => 'Position',
      'field_gender'   => 'Gender',
      'field_company'  => 'Company name'
    ];

    try {
      $_from = $sandbox['progress'];
      $_to   = $_from + $process_limit;

      for ($index = $_from; $index <= $_to; $index++) {
        if (!isset($customer_data[$index])) {
          break;
        }

        $row_data = $customer_data[$index];
        
        // Ignore rows with empty first name
        if (empty(self::getRowDataByFieldName($row_data, $column_map, 'First name'))) {
          $sandbox['progress']++;
          continue;
        }

        $standarized_data = [];

        foreach ($key_maps as $field_key => $sheet_key) {
          $cell_data = self::getRowDataByFieldName($row_data, $column_map, $sheet_key);
          $standarized_data[$field_key] = $cell_data;
        }

        $this->database
          ->insert('training_import_data')
          ->fields([
            'type' => 'customers',
            'data' => serialize($standarized_data),
          ])
          ->execute();

        $context['results']['queued']++;
        $sandbox['progress']++;
      }
    }
    catch (Exception $e) {
      $this->logger
        ->error('An error ocurred while processing: ' . $e->getMessage());
    }

    $context['message'] = '<h2>' . $this->t('Processing data...') . '</h2>';
    $context['message'] .= $this->t('Processed @c/@r rows.', [
      '@c' => $sandbox['progress'],
      '@r' => $sandbox['total'],
    ]);

    if ($sandbox['total']) {
      $context['finished'] = $sandbox['progress'] / $sandbox['total'];
    }
  }

  public function doImportBatch(int $process_limit, &$context) {
    $sandbox = &$context['sandbox'];

    if (empty($sandbox)) {
      $sandbox = [
        'progress' => 0,
        'total'    => 100000
      ];
      $context['results']['queued'] = 0;
    }

    usleep(5000);
    $sandbox['progress'] += 100;

    $context['message'] = '<h2>' . $this->t('Importing data...') . '</h2>';
    $context['message'] .= $this->t('Processed @c/@r rows.', [
      '@c' => $sandbox['progress'],
      '@r' => $sandbox['total'],
    ]);

    if ($sandbox['total']) {
      $context['finished'] = $sandbox['progress'] / $sandbox['total'];
    }

    $this->messenger()
      ->addMessage($process_limit);
  }

  /**
   * Reports the results of the data import operations.
   *
   * @param bool  $success
   * @param array $results
   * @param array $operations
   */
  public function doFinishBatch($success, $results, $operations) {
    if ($success) {
      $block_size  = 100;
      $queue_count = floor($results['queued'] / $block_size) + 1;

      for ($id = 0; $id < $queue_count; $id++) {
        $this->queue->createItem([
          'block_id'   => $id,
          'block_size' => $block_size
        ]);
      }

      $queued_msg = $this->formatPlural(
        $results['queued'],
        'One customer added to queue.',
        '@count customers added to queue.'
      );
      $this->messenger()
        ->addMessage($queued_msg);
    }

    $url = Url::fromRoute('system.cron_settings',
      [
        'attributes' => [
          'target' => '_blank',
        ],
      ]);

    $pass_link = Link::fromTextAndUrl($this->t('here'), $url)
      ->toString();

    $this->messenger()
      ->addWarning(
        $this->t(
          'Your data is queued to import. If you want to import now, manually trigger cron @here.',
          [
            '@here' => $pass_link,
          ]
        )
      );
  }
}
