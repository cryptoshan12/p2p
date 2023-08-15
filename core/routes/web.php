<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


//Crons
Route::get('cron/fiat-currency', 'CronController@fiatRate')->name('cron.fiat.rate');
Route::get('cron/crypto-currency', 'CronController@cryptoRate')->name('cron.crypto.rate');


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::namespace('Gateway')->prefix('ipn')->name('ipn.')->group(function () {

    Route::post('crypto', 'PaymentController@cryptoipn')->name('crypto');

});

// User Support Ticket
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@supportTicket')->name('ticket');
    Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});


/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
*/

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware('admin')->group(function () {
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications','AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all','AdminController@readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report','AdminController@requestReport')->name('request.report');
        Route::post('request-report','AdminController@reportSubmit');

        Route::get('system-info','AdminController@systemInfo')->name('system.info');

        //Advertisement Limit
        Route::get('advertisement-limit', 'ManageAdvertisementController@limitIndex')->name('ad.limit.index');
        Route::post('advertisement-limit/store', 'ManageAdvertisementController@limitStore')->name('ad.limit.store');
        Route::post('advertisement-limit/update/{id}', 'ManageAdvertisementController@limitUpdate')->name('ad.limit.update');

        //Advertisement
        Route::get('advertisements', 'ManageAdvertisementController@advertisements')->name('ad.index');
        Route::get('advertisement/{id}', 'ManageAdvertisementController@advertisementVew')->name('ad.view');
        Route::post('advertisement-update/{id}', 'ManageAdvertisementController@advertisementUpdate')->name('ad.update');
        Route::get('advertisements/search', 'ManageAdvertisementController@advertisementSearch')->name('ad.search');

        //Crypto Currency
        Route::get('crypto','CurrencyController@cryptoIndex')->name('crypto.index');
        Route::get('crypto/add-new','CurrencyController@cryptoNew')->name('crypto.new');
        Route::post('crypto/store','CurrencyController@cryptoStore')->name('crypto.store');
        Route::get('crypto/edit/{id}','CurrencyController@cryptoEdit')->name('crypto.edit');
        Route::post('crypto/update/{id}','CurrencyController@cryptoUpdate')->name('crypto.update');
        Route::get('crypto/search', 'CurrencyController@cryptoSearch')->name('crypto.search');
        Route::get('crypto/metamask','CurrencyController@metamask')->name('crypto.metamask'); //added new
        Route::get('crypto/metamask/edit/{id}','CurrencyController@metamaskEdit')->name('crypto.metamask.edit'); //added new
        Route::post('crypto/metamask/update/{id}','CurrencyController@metamaskUpdate')->name('crypto.metamask.update');

        //Fiat Currency
        Route::get('fiat', 'CurrencyController@fiatIndex')->name('fiat.currency.index');
        Route::post('fiat/store', 'CurrencyController@fiatStore')->name('fiat.currency.store');
        Route::post('fiat/update/{id}', 'CurrencyController@fiatUpdate')->name('fiat.currency.update');
        Route::get('fiat/search', 'CurrencyController@fiatSearch')->name('fiat.currency.search');

        //Fiat Gateway
        Route::get('fiat-gateway', 'CurrencyController@fiatGatewayIndex')->name('fiat.gateway.index');
        Route::post('fiat-gateway/store', 'CurrencyController@fiatGatewayStore')->name('fiat.gateway.store');
        Route::post('fiat-gateway/update/{id}', 'CurrencyController@fiatGatewayUpdate')->name('fiat.gateway.update');
        Route::get('fiat-gateway/search', 'CurrencyController@fiatGatewaySearch')->name('fiat.gateway.search');

        //Payment Window
        Route::get('payment-window', 'CurrencyController@paymentWindowIndex')->name('payment.window.index');
        Route::post('payment-window/store', 'CurrencyController@paymentWindowStore')->name('payment.window.store');
        Route::post('payment-window/update/{id}', 'CurrencyController@paymentWindowUpdate')->name('payment.window.update');
        Route::post('payment-window/remove', 'CurrencyController@paymentWindowRemove')->name('payment.window.remove');

        //Trade Request
        Route::get('trade-requests', 'TradeController@tradeRequests')->name('trade.request.index');
        Route::get('trade-requests/reported', 'TradeController@reportedTradeRequests')->name('trade.request.reported');
        Route::get('trade-requests/completed', 'TradeController@completedTradeRequests')->name('trade.request.completed');
        Route::get('trade-request-details/{id}', 'TradeController@tradeRequestDetails')->name('trade.request.details');
        Route::get('trade-request-details/release/{id}', 'TradeController@release')->name('trade.request.release');
        Route::get('trade-request-details/return/{id}', 'TradeController@return')->name('trade.request.return');
        Route::get('trade-request/search', 'TradeController@tradeRequestDetailsSearch')->name('trade.request.search');
        Route::post('trade-request-details/chat/store{id}', 'TradeController@tradeRequestChatStore')->name('trade.request.chat.store');
        

        // Chat File Download
        Route::get('/file-download/{id}', 'TradeController@download')->name('download');

        // Users Manager
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('user/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');

        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');
        Route::get('users/email-log/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('users/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');


        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function(){
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');

            Route::get('search', 'DepositController@search')->name('search');
            Route::get('date-search', 'DepositController@dateSearch')->name('dateSearch');

        });


        // WITHDRAW SYSTEM
        Route::name('withdraw.')->prefix('withdraw')->group(function(){
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');
        });

        // Report
        Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');
        Route::get('report/login/history', 'ReportController@loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/email/history', 'ReportController@emailHistory')->name('report.email.history');


        // Admin Support
        Route::get('tickets', 'SupportTicketController@tickets')->name('ticket');
        Route::get('tickets/pending', 'SupportTicketController@pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'SupportTicketController@closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'SupportTicketController@answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'SupportTicketController@ticketReplySend')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'SupportTicketController@ticketDownload')->name('ticket.download');
        Route::post('ticket/delete', 'SupportTicketController@ticketDelete')->name('ticket.delete');


        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.importLang');



        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');



        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
        Route::get('optimize', 'GeneralSettingController@optimize')->name('setting.optimize');

        // Api Setting
        Route::get('api-setting', 'GeneralSettingController@apiIndex')->name('api.index');
        Route::post('api-setting', 'GeneralSettingController@apiUpdate')->name('api.update');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css','GeneralSettingController@customCss')->name('setting.custom.css');
        Route::post('custom-css','GeneralSettingController@customCssSubmit');


        //Cookie
        Route::get('cookie','GeneralSettingController@cookie')->name('setting.cookie');
        Route::post('cookie','GeneralSettingController@cookieSubmit');


        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');



        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');


        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global');
        Route::get('sms-template/setting','SmsTemplateController@smsSetting')->name('sms.templates.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');


        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {


            Route::get('templates', 'FrontendController@templates')->name('templates');
            Route::post('templates', 'FrontendController@templatesActive')->name('templates.active');


            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');

            // Page Builder
            Route::get('manage-pages', 'PageBuilderController@managePages')->name('manage.pages');
            Route::post('manage-pages', 'PageBuilderController@managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'PageBuilderController@managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete', 'PageBuilderController@managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'PageBuilderController@manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'PageBuilderController@manageSectionUpdate')->name('manage.section.update');
        });
    });
});




/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
*/


Route::name('user.')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');
    Route::post('check-mail', 'Auth\RegisterController@checkUser')->name('checkUser');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');
});

Route::name('user.')->prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('dashboard', 'UserController@home')->name('home');

            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::get('change-password', 'UserController@changePassword')->name('change.password');
            Route::post('change-password', 'UserController@submitPassword');

            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');


            // // Deposit
            Route::any('deposit/history', 'UserController@depositHistory')->name('deposit.history');
            // // Deposit with metamask
            Route::post('/deposit/metamask', 'UserController@depositMetamask')->name('deposit.metamask');

            // Withdraw
            Route::get('/withdraw/history', 'UserController@withdrawLog')->name('withdraw.history');
            Route::get('/withdraw/{crypto}', 'UserController@withdraw')->name('withdraw');
            Route::post('/withdraw', 'UserController@withdrawStore')->name('withdraw.store');

            // Transactions
            Route::get('/transactions', 'UserController@transactionIndex')->name('transaction.index');
            Route::get('/transaction/{id}/{code}', 'UserController@singleTransaction')->name('transaction.single');

            //Wallets
            Route::get('/wallets', 'Gateway\PaymentController@wallets')->name('wallets');
            Route::get('/single-wallet/{id}/{code}', 'Gateway\PaymentController@singleWallet')->name('wallets.single');
            Route::get('/wallets/generate/{crypto}', 'Gateway\PaymentController@walletGenerate')->name('wallets.generate');


            // Advertisement
            Route::get('/advertisements', 'AdvertisementController@index')->name('advertisement.index');
            Route::get('/advertisement-new', 'AdvertisementController@new')->name('advertisement.new');
            Route::post('/advertisement-store', 'AdvertisementController@store')->name('advertisement.store');
            Route::get('/advertisement-edit/{id}', 'AdvertisementController@edit')->name('advertisement.edit');
            Route::post('/advertisement-update/{id}', 'AdvertisementController@update')->name('advertisement.update');
            Route::post('/advertisement-status', 'AdvertisementController@statusUpdate')->name('advertisement.status');

            // Trade Request
            Route::get('/trade-requests', 'TradeController@tradeRequests')->name('trade.requests');
            Route::get('/trade-request-details/{id}', 'TradeController@tradeRequestDetails')->name('trade.request.details');
            Route::get('/trade-request/new/{id}', 'TradeController@tradeRequestNew')->name('trade.request.new');
            Route::post('/trade-request/store/{id}', 'TradeController@tradeRequestStore')->name('trade.request.store');
            //added new
            Route::post('/chats/ajax', 'TradeController@chatsAjax')->name('chats.ajax');

            // Trade Request Operation
            Route::post('/trade-request/cancel', 'TradeController@tradeRequestCancel')->name('trade.request.cancel');
            Route::post('/trade-request/paid', 'TradeController@tradeRequestPaid')->name('trade.request.paid');
            Route::post('/trade-request/dispute', 'TradeController@tradeRequestDispute')->name('trade.request.dispute');
            Route::post('/trade-request/release', 'TradeController@tradeRequestRelease')->name('trade.request.release');

            // Trade Request Chat
            Route::post('/trade-request-chat/store/{id}', 'TradeController@tradeRequestChatStore')->name('trade.request.chat.store');

            // File Download
            Route::get('/file-download/{tradeId}/{id}', 'TradeController@download')->name('download');

        });
    });
});

// Supported Currency
Route::get('/supported-currency', 'AdvertisementController@supportedCurrency')->name('advertisement.supported.currency');

// Crypto Currency Wise Advertisements
Route::get('/currency-wise-buy-ad', 'SiteController@currencyWiseBuyAd')->name('advertisement.currency.buy');
Route::get('/currency-wise-sell-ad', 'SiteController@currencyWiseSellAd')->name('advertisement.currency.sell');

//Search Advertisement
Route::get('/search-ad', 'SiteController@searchAdvertisements')->name('advertisement.search');



//Policy
Route::get('policy/{slug}/{id}', 'SiteController@policyDetails')->name('policy.details');


Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');

Route::get('/cookie-accept', 'SiteController@cookieAccept')->name('cookie.accept');

Route::get('blog/{id}/{slug}', 'SiteController@blogDetails')->name('blog.details');

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');


Route::get('/{slug}', 'SiteController@pages')->name('pages');
Route::get('/', 'SiteController@index')->name('home');

Route::get('/{buysell}/{crypto}/{fiatgt?}/{fiat?}/{amount?}', 'SiteController@buySell')->name('buy.sell');
