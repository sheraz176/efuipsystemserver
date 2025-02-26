<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\AgentAuthController;
use App\Http\Controllers\Agent\AgentSalesController;
use App\Http\Controllers\Transaction\PaymentController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\SMS\SmsDelivery;
use App\Http\Controllers\SuperAdmin\SuperAdminAuth;
use App\Http\Controllers\SuperAdmin\SuperAdminReports;
use App\Http\Controllers\SuperAdmin\Refunds\ManageRefunds;
use App\Http\Controllers\SuperAdmin\UserManageController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\UnSubscription\ManagerUnSubscription;
use App\Http\Controllers\SuperAdmin\AgentsReports\AgentReportsController;
use App\Http\Controllers\SuperAdmin\Charts;
use App\Http\Controllers\SuperAdmin\TesalesAgentsController;
use App\Http\Controllers\bulkmanager\BulkManagerController;
use App\Http\Controllers\bulkmanager\bulkFileController;
use App\Http\Controllers\Agent\TeleSalesAgentController;
use App\Http\Controllers\CompanyManager\CompanyManagerAuthController;
use App\Http\Controllers\CompanyManager\CompanyManagerReportController;
use App\Http\Controllers\CompanyManager\CMExportController;
use App\Http\Controllers\CompanyManager\DashboardController;
use App\Http\Controllers\CompanyManager\SubscriptionChartController;
use App\Http\Controllers\BasicAgent\AgentAuthController as AgentAuthController2;
use App\Http\Controllers\BasicAgent\AgentSalesController as AgentSalesController2;
use App\Http\Controllers\BasicAgent\CustomerController;
use App\Http\Controllers\SuperAgent\SuperAgentAuthController;
use App\Http\Controllers\SuperAgent\SuperAgentDashboardController;
use App\Http\Controllers\SuperAgent\CustomerData;
use App\Http\Controllers\SuperAdmin\ExportController;
use App\Http\Controllers\SuperAgentL\SuperAgentAuthLController;
use App\Http\Controllers\SuperAgentL\SuperAgentDashboardLController;
use App\Http\Controllers\SuperAgentL\CustomerDataL;
use App\Http\Controllers\SuperAgentInterested\SuperAgentDashboardControllerInterested;
use App\Http\Controllers\SuperAgentInterested\SuperAgentAuthControllerInterested;
use App\Http\Controllers\SuperAgentInterested\CustomerDataInterested;
use App\Http\Controllers\SuperAdmin\LogsController;
use App\Http\Controllers\customerInformation\CustomerInformationController;
use App\Http\Controllers\BasicAgentL\AgentAuthBasicAgentLController;
use App\Http\Controllers\BasicAgentL\AgentSalesBasicAgentLController;
use App\Http\Controllers\BasicAgentL\CustomerBasicAgentLController;
use App\Http\Controllers\BasicAgentL\AutoDebitProcessController;
use App\Http\Controllers\SuperAdmin\processBulkRefund;
use App\Http\Controllers\SuperAdmin\RefundedController;
use App\Http\Controllers\SuperAdmin\ProcessBulkSubController;
use App\Http\Controllers\Agent\AgentRefundedController;
use App\Http\Controllers\Agent\AgentBulkManagerController;
use App\Http\Controllers\Agent\AgentbulkFileController;
use App\Http\Controllers\Agent\AgentprocessBulkRefund;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| TeleSales Agent Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Cache Routes
Route::get('/clear', function () {
    Artisan::call('config:cache');
 Artisan::call('cache:clear');
 Artisan::call('route:clear');
 Artisan::call('view:clear');
   return 'All cache cleared';
});



Route::prefix('refund-agent')->group(function () {
    Route::get('/login', [AgentAuthController::class, 'showLoginForm'])->name('agent.login');
    Route::post('/login', [AgentAuthController::class, 'login']);

    Route::middleware(['web', 'agent'])->group(function () {
        Route::get('/dashboard', [AgentAuthController::class, 'dashboard'])->name('agent.dashboard');
        Route::post('/logout', [AgentAuthController::class, 'logout'])->name('agent.logout');


        Route::get('/Refunded/Customer', [AgentRefundedController::class,'index'])->name('agent.refunded.customer');
        Route::get('/Refunded/Customer/Search', [AgentRefundedController::class,'search'])->name('agent.refunded.customer.search');
        Route::post('/refund/process', [AgentRefundedController::class, 'processRefund'])->name('agent.refund.process');

         //Start BulkManagerController
         Route::get('bulk/file/upload/index', [AgentBulkManagerController::class, 'index'])->name('agent.builkmanager.index');
         Route::get('bulk/file/upload/create', [AgentBulkManagerController::class, 'create'])->name('agent.builkmanager.create');
         Route::post('bulk/file/upload/store', [AgentBulkManagerController::class, 'store'])->name('agent.builkmanager.store');
         Route::get('bulk/file/upload/getData', [AgentBulkManagerController::class, 'getData'])->name('agent.builkmanager.getData');
         Route::post('/file-upload', [AgentbulkFileController::class, 'upload'])->name('agent.file.upload');

          Route::get('/process/bulk/refund/File', [AgentprocessBulkRefund::class, 'processfile'])->name('agent.process.bulk.refund.file');
          Route::post('/process/bulk/refund', [AgentprocessBulkRefund::class, 'bilkulfileRun'])->name('agent.process.bulk.refund');
          Route::get('/get-processed-results', [AgentprocessBulkRefund::class, 'getProcessedResults'])->name('agent.getProcessedResults');

          Route::get('/download-sample-csv', [LogsController::class, 'downloadSampleCsv'])
          ->name('agent.download.sample.csv');

          Route::get('bulk/file/upload/index/logs', [LogsController::class, 'agentbulkmanagerindex'])->name('agent.builkmanager.logsindex');
          Route::get('bulk/file/upload/logsData', [LogsController::class, 'agentbulkmanagerlogsData'])->name('agent.builkmanager.logsData');


          //END BulkManagerController



        // Route::get('/sales', [AgentSalesController::class, 'sales'])->name('agent.sales');
        // Route::get('/transaction', [AgentSalesController::class, 'transaction'])->name('agent.transaction');
        // Route::get('/sucesssales', [AgentSalesController::class, 'showAgentData'])->name('agent.sucesssales');
        // Route::get('/Failedsucesssales', [AgentSalesController::class, 'FailedAgentReports'])->name('agent.Failedsucesssales');
        // Route::post('/transaction-controller-route', [PaymentController::class, 'transactionController'])->name('transaction-controller-route');
        // Route::post('/sms-delivery-route', [SmsDelivery::class, 'smsDelivery'])->name('sms-delivery-route');
        // Route::post('/check-subscription', [SubscriptionController::class, 'checkSubscription'])->name('check-subscription');
        // Route::get('/overall-reports', [ReportsController::class, 'overall_report'])->name('agent.overall-reports');



    });
});

Route::prefix('basic-agent')->group(function () {
    Route::get('/login', [AgentAuthController2::class, 'showLoginForm'])->name('basic-agent.login');
    Route::post('/login', [AgentAuthController2::class, 'login'])->name('basic-agent.login-post');

    Route::group(['middleware' => ['auth:agent', 'check.agent.login']], function () {
        Route::get('/dashboard', [AgentAuthController2::class, 'dashboard'])->name('basic-agent.dashboard');
        Route::get('/sales', [AgentSalesController2::class, 'sales'])->name('basic-agent.sales');
        Route::get('/transaction', [AgentSalesController2::class, 'transaction'])->name('basic-agent.transaction');
        Route::post('/logout', [AgentAuthController2::class, 'logout'])->name('basic-agent.logout');
        Route::get('/sucesssales', [AgentSalesController2::class, 'showAgentData'])->name('basic-agent.sucesssales');
        Route::get('/Failedsucesssales', [AgentSalesController2::class, 'FailedAgentReports'])->name('basic-agent.Failedsucesssales');
        // routes/web.php
        Route::post('/save-customer', [CustomerController::class, 'saveCustomer'])->name('save-customer');
        Route::post('/check-subscription', [SubscriptionController::class, 'checkSubscription'])->name('check-subscription-basic');
        Route::get('/overall-reports', [ReportsController::class, 'overall_report_basic'])->name('basic-agent.overall-reports');

             // customer search info
             Route::get('/customer/info', [CustomerInformationController::class,'BasicAgentindex'])->name('basic-agent.customerinformation');
             Route::get('/customer/info/Search', [CustomerInformationController::class,'BasicAgentsearch'])->name('basic-agent.customerinformation.search');


    });
});

Route::prefix('basic-agent-l')->group(function () {
    Route::get('/login', [AgentAuthBasicAgentLController::class, 'showLoginForm'])->name('basic-agent-l.login');
    Route::post('/login', [AgentAuthBasicAgentLController::class, 'login'])->name('basic-agent-l.login-post');

    Route::group(['middleware' => ['auth:agent', 'check.agent.login']], function () {
        Route::get('/dashboard', [AgentAuthBasicAgentLController::class, 'dashboard'])->name('basic-agent-l.dashboard');
        Route::get('/agent/dashboard-data', [AgentAuthBasicAgentLController::class, 'getDashboardData'])->name('agent.dashboard.data');
        Route::get('/sales', [AgentSalesBasicAgentLController::class, 'sales'])->name('basic-agent-l.sales');
        Route::get('/transaction', [AgentSalesBasicAgentLController::class, 'transaction'])->name('basic-agent-l.transaction');
        Route::post('/logout', [AgentAuthBasicAgentLController::class, 'logout'])->name('basic-agent-l.logout');
        Route::get('/sucesssales', [AgentSalesBasicAgentLController::class, 'showAgentData'])->name('basic-agent-l.sucesssales');
        Route::get('/Failedsucesssales', [AgentSalesBasicAgentLController::class, 'FailedAgentReports'])->name('basic-agent-l.Failedsucesssales');
        // routes/web.php
        Route::post('/save-customer', [CustomerBasicAgentLController::class, 'saveCustomer'])->name('save-customer');
        Route::post('/check-subscription', [SubscriptionController::class, 'checkSubscription'])->name('check-subscription-basic');
        Route::get('/overall-reports', [ReportsController::class, 'overall_report_basic_agent_l'])->name('basic-agent-l.overall-reports');

        Route::get('/auto/debit/index', [AutoDebitProcessController::class, 'index'])->name('basic-agent-l.index');
        Route::post('/fetch-customer-data', [AutoDebitProcessController::class, 'fetchCustomerData'])->name('basic-agent-l.fetch_customer_data');
        Route::post('/consent-check', [AutoDebitProcessController::class, 'checkConsent'])->name('basic-agent-l.consent_check');
        Route::post('/auto/debit/request/check', [AutoDebitProcessController::class, 'requestcheck'])->name('basic_agent_l.request_check');

             // customer search info
       Route::get('/customer/info', [CustomerInformationController::class,'BasicAgentLindex'])->name('basic-agent-l.customerinformation');
       Route::get('/customer/info/Search', [CustomerInformationController::class,'BasicAgentLsearch'])->name('basic-agent-l.customerinformation.search');


    });
});


Route::prefix('super-admin')->group(function () {
    Route::get('/login', [SuperAdminAuth::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/login', [SuperAdminAuth::class, 'login']);

    Route::middleware(['auth:super_admin'])->group(function () {
        Route::get('/dashboard', [SuperAdminAuth::class, 'showDashboard'])->name('superadmin.dashboard');
        Route::post('/logout', [SuperAdminAuth::class, 'logout'])->name('superadmin.logout');

        Route::get('/dashboard/stats', [SuperAdminAuth::class, 'getStats'])->name('dashboard.stats');


            //Export all Data
            Route::post('export/active/subription', [ExportController::class, 'exportactivesubription'])->name('superadmin.export-active-subription');
            Route::post('export/complete/sale', [ExportController::class, 'exportcomplatesale'])->name('superadmin.export-complete.sale');
            Route::post('export/failed/data', [ExportController::class, 'exportgetFailedData'])->name('superadmin.export.failed-data');
            Route::post('export/companies/cancelled_data_export', [ExportController::class, 'companies_cancelled_data_export'])->name('superadmin.companies.cancelled-data-export');
            Route::post('export/RefundedDataExport', [ExportController::class, 'RefundedDataExport'])->name('superadmin.RefundedDataExport');
            Route::post('export/ManageRefundedDataExport', [ExportController::class, 'ManageRefundedDataExport'])->name('superadmin.ManageRefundedDataExport');
            Route::post('export/getDataCompanyExport', [ExportController::class, 'getDataCompanyExport'])->name('superadmin.getDataCompanyExport');
            Route::post('export/agents/get/data', [ExportController::class, 'agents_get_data_export'])->name('superadmin.agents-get-data-export');
            Route::post('export/agents/sale/data', [ExportController::class, 'agents_sales_data_export'])->name('superadmin.agents-sale-data-export');
            Route::post('export/companies/failed_data_export', [ExportController::class, 'companies_failed_data_export'])->name('superadmin.companies-failed-data-export');
            Route::post('export/export-recusive-charging-data', [ExportController::class, 'export_recusive_charing_data'])->name('superadmin.export-recusive-charging-data');
            Route::post('export/export-consent-number-data', [ExportController::class, 'export_consent_number_data'])->name('superadmin.export-consent-number-data');

            //END Export all Data

        Route::get('datatable', [SuperAdminReports::class, 'index'])->name('superadmin.datatable');
        Route::get('datatable/getData', [SuperAdminReports::class, 'getData'])->name('datatable.getData');

         //Start BulkManagerController
         Route::get('bulk/file/upload/index', [BulkManagerController::class, 'index'])->name('superadmin.builkmanager.index');
         Route::get('bulk/file/upload/create', [BulkManagerController::class, 'create'])->name('superadmin.builkmanager.create');
         Route::post('bulk/file/upload/store', [BulkManagerController::class, 'store'])->name('superadmin.builkmanager.store');
         Route::get('bulk/file/upload/getData', [BulkManagerController::class, 'getData'])->name('superadmin.builkmanager.getData');

         Route::post('/file-upload', [bulkFileController::class, 'upload'])->name('superadmin.file.upload');

          //END BulkManagerController

           //Start ProcessBulkSubController
           Route::get('bulk/processSubfile', [ProcessBulkSubController::class, 'processSubfile'])->name('superadmin.Subbuilkmanager.processSubfile');
           Route::post('ProcessSubfile/file/upload', [ProcessBulkSubController::class, 'upload'])->name('superadmin.Subbuilkmanager.upload');
           Route::post('/process/bulk/sub', [ProcessBulkSubController::class, 'bilkulfileRun'])->name('process.bulk.sub');
           Route::get('/get-processed-results/sub', [ProcessBulkSubController::class, 'getProcessedResults'])->name('getProcessedResults.sub');


           //End ProcessBulkSubController

        //Start Logs
        Route::get('auto/debit/api/logs', [LogsController::class, 'SuperAgentindex'])->name('superadmin.auto.debit.api.log');
        Route::get('auto/debit/api/logs/data', [LogsController::class, 'SuperAgentlogsData'])->name('superadmin.auto.debit.api.log.data');
        Route::get('payment/api/logs', [LogsController::class, 'Agentindex'])->name('superadmin.payment.api.log');
        Route::get('payment/api/logs/data', [LogsController::class, 'AgentlogsData'])->name('superadmin.payment.api.log.data');
        Route::get('bulk/file/upload/index/logs', [LogsController::class, 'bulkmanagerindex'])->name('superadmin.builkmanager.logsindex');
        Route::get('bulk/file/upload/logsData', [LogsController::class, 'bulkmanagerlogsData'])->name('superadmin.builkmanager.logsData');
        Route::get('Refund/button/upload/index/logs', [LogsController::class, 'buttonlogsindex'])->name('superadmin.Refundbutton.logsindex');
        Route::get('Refund/button/upload/logsData', [LogsController::class, 'buttonlogsData'])->name('superadmin.Refundbutton.logsData');
        Route::get('/download-sample-csv', [LogsController::class, 'downloadSampleCsv'])
        ->name('download.sample.csv');
        Route::get('auto/debit/Super/Agent/Name/Logs', [LogsController::class, 'SuperAgentName'])->name('superadmin.auto.debit.super.agent.name');
        Route::post('auto/debit/Super/Agent/Name/Logs/Export', [LogsController::class, 'export'])->name('superadmin.export-logs');
        Route::get('auto/debit/Super/Agent/Name/Ajax', [LogsController::class, 'SuperAgentNameAjax'])->name('superadmin.auto.debit.super.agent.SuperAgentNameAjax');
        Route::get('bulk/sub/api', [LogsController::class, 'bulksubapilogs'])->name('superadmin.bulk.sub.api');
        Route::get('bulk/sub/index', [LogsController::class, 'bulksubapilogsindex'])->name('superadmin.bulk.sub.index');

        //End Logs

        Route::get('datatable-failed', [SuperAdminReports::class, 'failed_transactions'])->name('superadmin.datatable-failed');
        Route::get('datatable-failed/getFailedData', [SuperAdminReports::class, 'getFailedData'])->name('datatable-failed.getFailedData');

        Route::get('datatable/ConsentData', [SuperAdminReports::class, 'ConsentDataIndex'])->name('superadmin.ConsentData');
        Route::get('datatable/ConsentDataGet', [SuperAdminReports::class, 'ConsentDataGet'])->name('superadmin.ConsentDataGet');



        //Complete Active Customers
        Route::get('complete-active-subscriptions', [SuperAdminReports::class, 'complete_active_subscription'])->name('superadmin.complete-active-subscriptions');
        Route::get('complete-active-subscriptions/getData', [SuperAdminReports::class, 'get_active_subscription_data'])->name('datatable.complete-active-subscriptions');

        Route::resource('company', CompanyProfileController::class);
        Route::resource('telesales-agents', TelesalesAgentController::class);

        //Telsales Emp Code Update
        Route::get('telesales-agents-emp/edit/{id}', [TesalesAgentsController::class, 'edit'])->name('superadmin.telesales-agents-emp.edit');
        Route::post('telesales-agents/update/emp', [TesalesAgentsController::class, 'update'])->name('superadmin.telesales-agents.update.emp');
        Route::get('telesales-agents-logout/edit/{id}', [TesalesAgentsController::class, 'Agentlogout'])->name('superadmin.telesales-agents-logout.edit');
        Route::get('telesales-agents-lnActive/edit/{id}', [TesalesAgentsController::class, 'InActive'])->name('superadmin.telesales-agents-Inactive.edit');
        Route::get('datatable/basic/agent/data', [TesalesAgentsController::class, 'AgentData'])->name('superadmin.basic.agent.data');
        //End Telsales Emp Code Update


          //User Managements Company Manager
          Route::get('company_manager/create', [UserManageController::class, 'company_manager_create'])->name('superadmin.company_manager_create');
          Route::get('company_manager/index', [UserManageController::class, 'company_manager_index'])->name('superadmin.company_manager_index');
          Route::post('company_manager/store', [UserManageController::class, 'company_manager_store'])->name('superadmin.company_manager_store');
         //User Managements Super Agent
         Route::get('super_agent/create', [UserManageController::class, 'super_agent_create'])->name('superadmin.super_agent_create');
         Route::get('super_agent/index', [UserManageController::class, 'super_agent_index'])->name('superadmin.super_agent_index');
         Route::post('super_agent/store', [UserManageController::class, 'super_agent_store'])->name('superadmin.super_agent_store');


        //RecusiveChargingData Report
        Route::get('recusive/chargingdataindex', [SuperAdminReports::class, 'recusive_charging_data_index'])->name('superadmin.recusive-charging-data-index');
        Route::get('recusive/getchargingdataindex', [SuperAdminReports::class, 'get_recusive_charging_data'])->name('superadmin.get-recusive-charging-data');

        //Company Route Controller
        Route::get('companies-reports', [SuperAdminReports::class, 'companies_reports'])->name('superadmin.companies-reports');
        Route::get('companies-reports/getDataCompany', [SuperAdminReports::class, 'getDataCompany'])->name('companies-reports.getDataCompany');

        Route::get('companies-failed-reports', [SuperAdminReports::class, 'companies_failed_reports'])->name('superadmin.companies-failed-reports');
        Route::get('companies-failed-reports/companies-failed-data', [SuperAdminReports::class, 'companies_failed_data'])->name('companies-reports.companies-failed-data');

        Route::get('companies-cancelled-reports', [SuperAdminReports::class, 'companies_unsubscribed_reports'])->name('superadmin.companies-cancelled-reports');
        Route::get('companies-cancelled-reports/companies-cancelled-data', [SuperAdminReports::class, 'companies_cancelled_data'])->name('superadmin.companies-cancelled-data');




        Route::get('manage-refunds', [ManageRefunds::class, 'index'])->name('superadmin.manage-refunds');
        Route::get('manage-refunds/getRefundData', [ManageRefunds::class, 'getRefundData'])->name('manage-refunds.getRefundData');
        Route::get('refunded/unsubscribe-now/{subscriptionId}', [ManagerUnSubscription::class,'unsubscribeNow'])->name('refunded.unsubscribe-now');


        Route::get('refunds-reports', [ManageRefunds::class, 'refundReports'])->name('superadmin.refunds-reports');
        Route::get('manage-refunds/getRefundedData', [ManageRefunds::class, 'getRefundedData'])->name('manage-refunds.getRefundedData');



        Route::get('agents-reports', [AgentReportsController::class, 'agents_Subscriptions'])->name('superadmin.agents-reports');
        Route::get('companies-reports/agents-get-data', [AgentReportsController::class, 'agents_get_data'])->name('companies-reports.agents-get-data');

        Route::get('agents-sales-request', [AgentReportsController::class, 'agents_sales_request'])->name('superadmin.agents-sales-request');
        Route::get('companies-reports/agents-sales-data', [AgentReportsController::class, 'agents_sales_data'])->name('companies-reports.agents-sales-data');

        Route::get('get-subscription-chart-data', [Charts::class, 'getSubscriptionChartData'])->name('superadmin.get-subscription-chart-data');

        Route::get('/chart-data', [Charts::class, 'getChartData'])->name('chart.data');
        Route::get('/Line/chart-data', [Charts::class, 'getLineChartData'])->name('superadmin.revinuechart');

        Route::get('/Line/chart/Recusive/Charging', [Charts::class, 'RecusiveChargingChart'])->name('superadmin.recusive.charging');
        Route::get('/Line/chart/low/balance', [Charts::class, 'LowBalaceChart'])->name('superadmin.low.balance');




        Route::get('getMonthlyActiveSubscriptionChartData', [Charts::class, 'getMonthlyActiveSubscriptionChartData'])->name('superadmin.getMonthlyActiveSubscriptionChartData');
        Route::get('getMonthlySubscriptionUnsubscriptionChartData', [Charts::class, 'getMonthlySubscriptionUnsubscriptionChartData'])->name('superadmin.getMonthlySubscriptionUnsubscriptionChartData');

          // customer search info
          Route::get('/customer/information', [CustomerInformationController::class,'index'])->name('superadmin.customerinformation');
          Route::get('/customer/information/Search', [CustomerInformationController::class,'search'])->name('superadmin.customerinformation.search');

          Route::get('/process/bulk/refund/File', [processBulkRefund::class, 'processfile'])->name('process.bulk.refund.file');
          Route::post('/process/bulk/refund', [processBulkRefund::class, 'bilkulfileRun'])->name('process.bulk.refund');
          Route::get('/get-processed-results', [processBulkRefund::class, 'getProcessedResults'])->name('getProcessedResults');

          Route::get('/Refunded/Customer', [RefundedController::class,'index'])->name('superadmin.refunded.customer');
          Route::get('/Refunded/Customer/Search', [RefundedController::class,'search'])->name('superadmin.refunded.customer.search');
          Route::post('/refund/process', [RefundedController::class, 'processRefund'])->name('superadmin.refund.process');




    });
});


Route::prefix('company-manager')->group(function () {
    Route::get('login', [CompanyManagerAuthController::class, 'showLoginForm'])->name('company.manager.login.form');
    Route::post('login', [CompanyManagerAuthController::class, 'login'])->name('company-manager-login');

    Route::middleware(['auth.company_manager'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('company-manager-dashboard');
        Route::post('logout', [CompanyManagerAuthController::class, 'logout'])->name('company.manager.logout');

        Route::get('ActiveAgent', [DashboardController::class, 'ActiveAgent'])->name('company-manager-ActiveAgent');
        Route::get('active/agent/Data', [DashboardController::class, 'AgentData'])->name('company.manager.agent.data');

         // Refundes Route
        Route::get('refunded/new/unsubscribe-now/{subscriptionId}', [ManagerUnSubscription::class,'unsubscribeNow'])->name('refunded.unsubscribe-now-new');


        Route::get('dashboard/ajex', [DashboardController::class, 'ajex'])->name('company.manager.ajex');
        Route::get('netentrooment/chart', [DashboardController::class, 'NetEnrollment'])->name('company.manager.netenrollment.chart');
        Route::get('refundedCustomers/chart', [DashboardController::class, 'RefundedCustomers'])->name('company.manager.refundedcustomers.chart');


        // Company Manager Intersted Customer
        Route::get('today/customer', [DashboardController::class, 'today_interested_customer'])->name('company-manager.today-interested-customer');
        Route::get('today/deduction/customer', [DashboardController::class, 'today_deduction_interested_customer'])->name('company-manager.deduction-interested-customer');

          // Company Manager Graphic
        Route::get('getMonthlyActiveSubscriptionChartData', [SubscriptionChartController::class, 'getMonthlyActiveSubscriptionChartData'])->name('companymanager.getMonthlyActiveSubscriptionChartData');
        Route::get('get-subscription-chart-data', [SubscriptionChartController::class, 'getSubscriptionChartData'])->name('companymanager.get-subscription-chart-data');

         //Complete Sales for Company Manager
         Route::get('CompleteSalesindex', [CompanyManagerReportController::class, 'complete_sales_index'])->name('companymanager.CompleteSalesindex');
         Route::get('CompleteSalesindex/getData', [CompanyManagerReportController::class, 'getData'])->name('companymanager.getData');

          //Complete Failed Report
         Route::get('datatable-failed', [CompanyManagerReportController::class, 'failed_transactions'])->name('company-manager.datatable-failed');
         Route::get('datatable-failed/getFailedData', [CompanyManagerReportController::class, 'getFailedData'])->name('company-manager.getFailedData');

         //Complete Active Customers
         Route::get('complete-active-subscriptions', [CompanyManagerReportController::class, 'complete_active_subscription'])->name('company-manager.complete-active-subscriptions');
         Route::get('complete-activecustomerdataget', [CompanyManagerReportController::class, 'activecustomerdataget'])->name('company-manager.activecustomerdataget');

          //Complete Cancelled Report
         Route::get('companies-cancelled-reports', [CompanyManagerReportController::class, 'companies_unsubscribed_reports'])->name('company-manager.companies-cancelled-reports');
         Route::get('companies-cancelled-reports/companies-cancelled-data', [CompanyManagerReportController::class, 'companies_cancelled_data'])->name('company-manager.companies-cancelled-data');

        //Complete Refund Report
         Route::get('refunds-reports', [CompanyManagerReportController::class, 'refundReports'])->name('company-manager.refunds-reports');
         Route::get('manage-refunds/getRefundedData', [CompanyManagerReportController::class, 'getRefundedData'])->name('company-manager.getRefundedData');

         //Complete Manage Refund Report
         Route::get('manage-refunds', [CompanyManagerReportController::class, 'manage_refund_index'])->name('company-manager.manage-refunds');
         Route::get('manage-refunds/getRefundData', [CompanyManagerReportController::class, 'getRefundData'])->name('company-manager.manage-refunds.getRefundData');

         //Complete Agent Report
         Route::get('agents-reports', [CompanyManagerReportController::class, 'agents_Subscriptions'])->name('company-manager.agents-reports');
         Route::get('companies-reports/agents-get-data', [CompanyManagerReportController::class, 'agents_get_data'])->name('company-manager.agents-get-data');

        //Complete Sale Report
         Route::get('agents-sales-request', [CompanyManagerReportController::class, 'agents_sales_request'])->name('company-manager.agents-sales-request');
         Route::get('companies-reports/agents-sales-data', [CompanyManagerReportController::class, 'agents_sales_data'])->name('company-manager.companies-reports.agents-sales-data');

         //Complete Tesales Agents
         Route::get('check-agent-status', [CompanyManagerReportController::class, 'check_agent_status'])->name('company-manager.check-agent-status');

           //Export all Data
           Route::post('export/active/subription', [CMExportController::class, 'exportactivesubription'])->name('company-manager.export-active-subription');
           Route::post('export/complete/sale', [CMExportController::class, 'exportcomplatesale'])->name('company-manager.export-complete.sale');\
           Route::post('export/failed/data', [CMExportController::class, 'exportgetFailedData'])->name('company-manager.export.failed-data');
           Route::post('export/companies/cancelled_data_export', [CMExportController::class, 'companies_cancelled_data_export'])->name('company-manager.companies.cancelled-data-export');
           Route::post('export/RefundedDataExport', [CMExportController::class, 'RefundedDataExport'])->name('company-manager.RefundedDataExport');
           Route::post('export/ManageRefundedDataExport', [CMExportController::class, 'ManageRefundedDataExport'])->name('company-manager.ManageRefundedDataExport');
           Route::post('export/getDataCompanyExport', [CMExportController::class, 'getDataCompanyExport'])->name('company-manager.getDataCompanyExport');
           Route::post('export/agents/get/data', [CMExportController::class, 'agents_get_data_export'])->name('company-manager.agents-get-data-export');
           Route::post('export/agents/sale/data', [CMExportController::class, 'agents_sales_data_export'])->name('company-manager.agents-sale-data-export');
           Route::post('export/companies/failed_data_export', [CMExportController::class, 'companies_failed_data_export'])->name('company-manager.companies-failed-data-export');
           Route::post('export/export-recusive-charging-data', [CMExportController::class, 'export_recusive_charing_data'])->name('company-manager.export-recusive-charging-data');

           //END Export all Data

             // customer search info
        Route::get('/customer/info', [CustomerInformationController::class,'CompanyMangerindex'])->name('company-manager.customerinformation');
        Route::get('/customer/info/Search', [CustomerInformationController::class,'CompanyMangersearch'])->name('company-manager.customerinformation.search');




    });
});




Route::prefix('super-agent')->group(function () {
    Route::get('/login', [SuperAgentAuthController::class, 'showLoginForm'])->name('super_agent.login');
    Route::post('/login', [SuperAgentAuthController::class, 'login'])->name('super_agent.login.submit');
    Route::post('/logout', [SuperAgentAuthController::class, 'logout'])->name('super_agent.logout');

    // Route group for SuperAgent dashboard requiring authentication
    Route::middleware(['super_agent_auth'])->group(function () {
        Route::get('/dashboard', [SuperAgentDashboardController::class, 'index'])->name('super_agent.dashboard');
        Route::get('/customer-form', [CustomerData::class, 'showForm'])->name('super_agent.showForm');;
        Route::post('/fetch-customer-data', [CustomerData::class, 'fetchCustomerData'])->name('super_agent.fetch_customer_data');


    });
});




Route::prefix('super-agent-l')->group(function () {
    Route::get('/login', [SuperAgentAuthLController::class, 'showLoginForm'])->name('super_agent_l.login');
    Route::post('/login', [SuperAgentAuthLController::class, 'login'])->name('super_agent_l.login.submit');
    Route::post('/logout', [SuperAgentAuthLController::class, 'logout'])->name('super_agent_l.logout');

    // Route group for SuperAgent L dashboard requiring authentication
    Route::middleware(['super_agent_auth'])->group(function () {
        Route::get('/dashboard', [SuperAgentDashboardLController::class, 'index'])->name('super_agent_l.dashboard');
        Route::get('/customer-form', [CustomerDataL::class, 'showForm'])->name('super_agent_l.showForm');;
        Route::post('/fetch-customer-data', [CustomerDataL::class, 'fetchCustomerData'])->name('super_agent_l.fetch_customer_data');



    });
});

 // Route group for super-agent-Interested dashboard requiring authentication

Route::prefix('super-agent-Interested')->group(function () {
    Route::get('/Interested/login', [SuperAgentAuthControllerInterested::class, 'showLoginForm'])->name('super_agent_interested.login');
    Route::post('/Interested/login', [SuperAgentAuthControllerInterested::class, 'login'])->name('super_agent_interested.login.submit');
    Route::post('/Interested/logout', [SuperAgentAuthControllerInterested::class, 'logout'])->name('super_agent_interested.logout');


    Route::middleware(['super_agent_auth'])->group(function () {
        Route::get('/Interested/dashboard', [SuperAgentDashboardControllerInterested::class, 'index'])->name('super_agent_interested.dashboard');
        Route::get('/Interested/customer-form', [CustomerDataInterested::class, 'showForm'])->name('super_agent_interested.showForm');;
        Route::post('/Interested/fetch-customer-data', [CustomerDataInterested::class, 'fetchCustomerData'])->name('super_agent_interested.fetch_customer_data');
        Route::post('/Interested/Interested-customer-data', [CustomerDataInterested::class, 'interestedCustomerData'])->name('super_agent_interested.interested_customer_data');

    });
});




// --------------------------------------------------------------------------
