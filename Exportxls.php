<?php
/**
 * Gratifi CRE
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available on the World Wide Web at:
 * http://opensource.org/licenses/osl-3.0.php
 * If you are unable to access it on the World Wide Web, please send an email
 * To: gratifienquiry@giftease.com  We will send you a copy of the source file.
 * @category   CRE module
 * @package    Gratifi_Reports
 * @copyright  Copyright (c) 2018 Giftease Technologies Pvt Ltd., India
 * https://www.gratifi.com
 * @license    proprietary
 * @author     Gratifi <gratifienquiry@giftease.com>
 */

namespace Gratifi\Reports\Controller\EmployeePerformance;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Gratifi\Company\Helper\Data as CompanyHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Gratifi\Reports\Helper\TeamPerformance as TeamPerformanceHelper;
use Gratifi\Reports\Helper\Data as ReportsHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Gratifi\Reports\Helper\DistributorPerformance as DistributorPerformanceHelper;

class Exportxls extends \Gratifi\Rolespermission\Controller\AbstractAction
{
    const COMPANY_RESOURCE = 'Gratifi_Reports::employee_performance_on_campaign';
    

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CompanyHelper
     */
    protected $companyHelper;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var TeamPerformanceHelper
     */
    protected $teamPerformanceHelper;

    /**
     * @var ReportsHelper
     */
    protected $reportsHelper;
     /**
     * @var MilestoneCollection
     */
    protected $milestoneCollection;

    /**
     * @var MilestoneCollection
     */
    protected $fileFactory;

    /**
     * @var DistributorPerformanceHelper
     */
    protected $distributorPerformanceHelper;
    
    /**
     * 
     * @param Context $context
     * @param \Gratifi\Rolespermission\Model\CheckPermission $checkPermission
     * @param \Psr\Log\LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param CompanyHelper $companyHelper
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        \Gratifi\Rolespermission\Model\CheckPermission $checkPermission,
        \Psr\Log\LoggerInterface $logger,
        PageFactory $resultPageFactory,
        CompanyHelper $companyHelper,
        JsonFactory $jsonFactory,
        TeamPerformanceHelper $teamPerformanceHelper,
        ReportsHelper $reportsHelper,
        \Gratifi\Campaign\Model\ResourceModel\Milestone\Collection $milestoneCollection,
        FileFactory $fileFactory,
        DateTime $dateTime,
        DistributorPerformanceHelper $distributorPerformanceHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->companyHelper = $companyHelper;
        $this->jsonFactory = $jsonFactory;
        $this->teamPerformanceHelper = $teamPerformanceHelper;
        $this->reportsHelper = $reportsHelper;
        $this->milestoneCollection = $milestoneCollection;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->distributorPerformanceHelper = $distributorPerformanceHelper;
        parent::__construct($context,$checkPermission, $logger);
    }

    /**
     * Generate Report on Ajax request on basis of campaign selected
         *
     * @return object/boolean
     */
    public function execute() 
    {
        $campaignId = $this->getRequest()->getParam('campaignId');
        
        
        $spreadsheet = new Spreadsheet();
        $Excel_writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();
        // $activeSheet->setCellValue('A1', 'Product Name');
        // $activeSheet->setCellValue('B1', 'Product SKU');
        // $activeSheet->setCellValue('C1', 'Product Price');
        

        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $media = $objectManager->get('Magento\Store\Model\StoreManagerInterface')
        //             ->getStore()
        //             ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        

        //$path=$media.'import/Order_Export.Xlsx';

        
        $companyId = $this->companyHelper->getCompanyId();
        $customerId = $this->companyHelper->getCustomerId();
        $targetMiestone = $this->milestoneCollection->getDealersTargetMilestone($campaignId,$companyId,$customerId,self::COMPANY_RESOURCE); 
        $achievedMiestone = $this->milestoneCollection->getDealersAchievedMilestone($campaignId,$companyId,$customerId,self::COMPANY_RESOURCE); 
        //echo '<pre>';
        // print_r($achievedMiestone);
        // die;
        $count = 0;
        $lastFrom = null;
        $achived_milestone = [];
        $previousId = '';


        foreach ($achievedMiestone as $item) {
            

            if ($previousId == $item['customer_id']) {
                $achived_milestone[$item['customer_id']][$count]['month'] = $item['month'];
                $achived_milestone[$item['customer_id']][$count]['target_value'] = $item['target_value'];
                $achived_milestone[$item['customer_id']][$count]['campaign_name'] = $item['campaign_name'];
                $achived_milestone[$item['customer_id']][$count]['customer_name'] = $item['customer_name'];
                $achived_milestone[$item['customer_id']][$count]['Job Title'] = $item['Job Title'];
                $count++;
            }else{  
                $count =0;
            }
            $previousId = $item['customer_id'];
        }
        

        $milestones = array_unique(array_column($this->distributorPerformanceHelper->getMilestones($campaignId), 'label'));
        echo "<pre>";
        print_r($achived_milestone);
        // print_r($milestones);
        die;

        $activeSheet->setCellValue('B1', 'APR');
        $activeSheet->setCellValue('C1', 'MAY');
        $activeSheet->setCellValue('D1', 'JUNE');
        $activeSheet->setCellValue('E1', 'JULY');
        $activeSheet->setCellValue('F1', 'AUG');
        $activeSheet->setCellValue('G1', 'SEPT');
        $activeSheet->setCellValue('H1', 'OCT');
        $activeSheet->setCellValue('I1', 'NOV');
        $activeSheet->setCellValue('J1', 'DEC');
        $activeSheet->setCellValue('K1', 'JAN');
        $activeSheet->setCellValue('L1', 'FEB');
        $activeSheet->setCellValue('M1', 'MAR');

        foreach ($achived_milestone as $key => $value) {
            foreach ($value as $key1 => $value1) {
                    $activeSheet->setCellValue('A'.$key, $value1['customer_name']);                    
                    $activeSheet->setCellValue('A'.$key, $value1['customer_name']);                    
                }    
        }

        
        $filename = 'Campaign.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='. $filename);
        header('Cache-Control: max-age=0')  ;
        $excel_path = '/var/www/html/rathi/pub/media/'.$filename;
        $Excel_writer->save($excel_path); 

        

//        $arr2 = [];
//        $count = 0;
//        foreach ($achievedMiestone as $key => $achieved) {
//            //$arr2[$achieved['customer_id']] = $achieved['month'];
//            $arr2[$key]['customer_id'] = $achieved['month'];
//        }
//        print_r($arr2);
//        die;
       

    }
}
    