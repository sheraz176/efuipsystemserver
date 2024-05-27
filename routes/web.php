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

Route::prefix('agent')->group(function () {
    Route::get('/login', [AgentAuthController::class, 'showLoginForm'])->name('agent.login');
    Route::post('/login', [AgentAuthController::class, 'login']);

    Route::middleware(['web', 'agent'])->group(function () {
        Route::get('/dashboard', [AgentAuthController::class, 'dashboard'])->name('agent.dashboard');
        Route::get('/sales', [AgentSalesController::class, 'sales'])->name('agent.sales');
        Route::get('/transaction', [AgentSalesController::class, 'transaction'])->name('agent.transaction');
        Route::post('/logout', [AgentAuthController::class, 'logout'])->name('agent.logout');
        Route::get('/sucesssales', [AgentSalesController::class, 'showAgentData'])->name('agent.sucesssales');
        Route::get('/Failedsucesssales', [AgentSalesController::class, 'FailedAgentReports'])->name('agent.Failedsucesssales');

        Route::post('/transaction-controller-route', [PaymentController::class, 'transactionController'])->name('transaction-controller-route');

        Route::post('/sms-delivery-route', [SmsDelivery::class, 'smsDelivery'])->name('sms-delivery-route');
        Route::post('/check-subscription', [SubscriptionController::class, 'checkSubscription'])->name('check-subscription');
        Route::get('/overall-reports', [ReportsController::class, 'overall_report'])->name('agent.overall-reports');



    });
});

Route::prefix('basic-agent')->group(function () {
    Route::get('/login', [AgentAuthController2::class, 'showLoginForm'])->name('basic-agent.login');
    Route::post('/login', [AgentAuthController2::class, 'login'])->name('basic-agent.login-post');

    Route::middleware(['web', 'agent'])->group(function () {
        Route::get('/dashboard', [AgentAuthController2::class, 'dashboard'])->name('basic-agent.dashboard');
        Route::get('/sales', [AgentSalesController2::class, 'sales'])->name('basic-agent.sales');
        Route::get('/transaction', [AgentSalesController2::class, 'transaction'])->name('basic-agent.transaction');
        Route::post('/logout', [AgentAuthController2::class, 'logout'])->name('basic-agent.logout');
        Route::get('/sucesssales', [AgentSalesController2::class, 'showAgentData'])->name('basic-agent.sucesssales');
        Route::get('/Failedsucesssales', [AgentSalesController2::class, 'FailedAgentReports'])->name('basic-agent.Failedsucesssales');
        // routes/web.php
        Route::post('/save-customer', [CustomerController::class, 'saveCustomer'])->name('save-customer');
        Route::post('/check-subscription', [SubscriptionController::class, 'checkSubscription'])->name('check-subscription-basic');
        Route::get('/overall-reports', [ReportsController::class, 'overall_report'])->name('basic-agent.overall-reports');


    });
});


Route::prefix('super-admin')->group(function () {
    Route::get('/login', [SuperAdminAuth::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/login', [SuperAdminAuth::class, 'login']);

    Route::middleware(['auth:super_admin'])->group(function () {
        Route::get('/dashboard', [SuperAdminAuth::class, 'showDashboard'])->name('superadmin.dashboard');
        Route::post('/logout', [SuperAdminAuth::class, 'logout'])->name('superadmin.logout');

            //Export all Data
            Route::post('export/active/subription', [ExportController::class, 'exportactivesubription'])->name('superadmin.export-active-subription');
            Route::post('export/complete/sale', [ExportController::class, 'exportcomplatesale'])->name('superadmin.export-complete.sale');\
            Route::post('export/failed/data', [ExportController::class, 'exportgetFailedData'])->name('superadmin.export.failed-data');
            Route::post('export/companies/cancelled_data_export', [ExportController::class, 'companies_cancelled_data_export'])->name('superadmin.companies.cancelled-data-export');
            Route::post('export/RefundedDataExport', [ExportController::class, 'RefundedDataExport'])->name('superadmin.RefundedDataExport');
            Route::post('export/ManageRefundedDataExport', [ExportController::class, 'ManageRefundedDataExport'])->name('superadmin.ManageRefundedDataExport');
            Route::post('export/getDataCompanyExport', [ExportController::class, 'getDataCompanyExport'])->name('superadmin.getDataCompanyExport');
            Route::post('export/agents/get/data', [ExportController::class, 'agents_get_data_export'])->name('superadmin.agents-get-data-export');
            Route::post('export/agents/sale/data', [ExportController::class, 'agents_sales_data_export'])->name('superadmin.agents-sale-data-export');
            Route::post('export/companies/failed_data_export', [ExportController::class, 'companies_failed_data_export'])->name('superadmin.companies-failed-data-export');
            Route::post('export/export-recusive-charging-data', [ExportController::class, 'export_recusive_charing_data'])->name('superadmin.export-recusive-charging-data');


            //END Export all Data

        Route::get('datatable', [SuperAdminReports::class, 'index'])->name('superadmin.datatable');
        Route::get('datatable/getData', [SuperAdminReports::class, 'getData'])->name('datatable.getData');


        Route::get('datatable-failed', [SuperAdminReports::class, 'failed_transactions'])->name('superadmin.datatable-failed');
        Route::get('datatable-failed/getFailedData', [SuperAdminReports::class, 'getFailedData'])->name('datatable-failed.getFailedData');

        //Complete Active Customers
        Route::get('complete-active-subscriptions', [SuperAdminReports::class, 'complete_active_subscription'])->name('superadmin.complete-active-subscriptions');
        Route::get('complete-active-subscriptions/getData', [SuperAdminReports::class, 'get_active_subscription_data'])->name('datatable.complete-active-subscriptions');

        Route::resource('company', CompanyProfileController::class);
        Route::resource('telesales-agents', TelesalesAgentController::class);

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



        Route::get('getMonthlyActiveSubscriptionChartData', [Charts::class, 'getMonthlyActiveSubscriptionChartData'])->name('superadmin.getMonthlyActiveSubscriptionChartData');
        Route::get('getMonthlySubscriptionUnsubscriptionChartData', [Charts::class, 'getMonthlySubscriptionUnsubscriptionChartData'])->name('superadmin.getMonthlySubscriptionUnsubscriptionChartData');


    });
});


Route::prefix('company-manager')->group(function () {
    Route::get('login', [CompanyManagerAuthController::class, 'showLoginForm'])->name('company.manager.login.form');
    Route::post('login', [CompanyManagerAuthController::class, 'login'])->name('company-manager-login');

    Route::middleware(['auth.company_manager'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('company-manager-dashboard');
        Route::post('logout', [CompanyManagerAuthController::class, 'logout'])->name('company.manager.logout');

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






// --------------------------------------------------------------------------
