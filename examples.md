# Empyr API Examples

[Users](#users) | 
[Venues](#venues) | 
[Billing Utilities](#billing-accounts) |
[Cards](#cards) | 
[Devices](#devices) |
[Fundraisers](#fundraisers) |
[Invoices](#invoices) |
[Metros](#metros) |
[Offers](#offers) |
[Reports](#reports) |
[Subscriptions](#subscriptions) |
[Utilities](#utilities) |

### Users
* `$data = Empyr::user()->lookup( 'email@domain.com' );`
* `$data = Empyr::user()->user( 12345 );`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->alerts();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->donations();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->forgotPassword();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->listOAuth();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->notificationSettings();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->payments();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->rewardList();`
* `$data = Empyr::user()->search('', ['numResults' => 10, 'offset' => 20 ] );`
* `signupWithCard * Not working`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->friendsList();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->fundraiserHistory();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->leaderboard();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->recommendations(['businesses' => 12345 ]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->summary();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->transactions( ['startDate' => '2017/01/01' ]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->venueHistory();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->addReward(['amount' => 1.35]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->alertsDismiss();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->friendApprove(['user' => 12345 ]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->friendDeny(['user' => 12345] );`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->donate(); *  Didn't test.`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->invite(); *  Didn't test.`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->offerLink(); *  Didn't test.`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->offerLinksList();`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->oauthLink(); *  Didn't test.`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->oauthUnlink(); *  Didn't test.`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->friendRequest(['user' => 12345]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->offerUnlink(['user' => 12345]);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->update(['firstName' => 'Bill', 'lastName' => 'Van Pelt']);`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->updatePhoto('/path/to/image/headshot.png');`
* `$data = Empyr::user( ['email' => 'email@domain.com'] )->updatePassword(['password' => 'password', 'newPassword' => 'test123']);`
* `$data = Empyr::user()->add(['firstName' => 'Bill', 'lastName' => 'Van Pelt', 'email' => 'email@domain.com', 'address.postalCode' => '12345'])`

### Venues
* `$data = Empyr::venue(['venue' => 12345])->venue();`
* `$data = Empyr::venue()->search(['query' => '']);`
* `$data = Empyr::venue()->searchByPhone('123-456-7890');`
* `$data = Empyr::venue()->segmented();`
* `$data = Empyr::venue(['venue' => 12345])->summary();`
* `$data = Empyr::venue(['venue' => 12345])->userVenueTotals();`
* `$data = Empyr::venue()->add([
'name' => 'Test',
'owner' => 12345,
'address.streetName' => '123 My St.',
'address.postalCode' => '12345',
'description' => 'Delicious food',
'priceRange' => '$$',
'fullPhone' => '(123) 456-7890',
]);`
* `$data = Empyr::venue(['venue' => 12345])->addPhoto('/path/to/image/one-1.jpg', ['type' => 'LOGO']);`
* `$data = Empyr::venue(['venue' => 12345, 'email' => 'name@domain.com'])->bookmark();`
* `$data = Empyr::venue(['venue' => 12345, 'email' => 'name@domain.com'])->removeBookmark();`
* `$data = Empyr::venue(['venue' => 12345])->removePhoto(0, [ 'media' => 'LOGO' ]);`
* `$data = Empyr::venue(['venue' => 12345])->update([
'name' => 'Test',
'owner' => 12345,
'address.streetName' => '123 My St.',
'address.postalCode' => '12345',
'description' => 'Delicious food',
'priceRange' => '$',
'fullPhone' => '(123) 456-7890',
]);`

### Billing Accounts
* `$data = EmpyrPartner::billingAccount()->billingAccount(12345);`
* `$data = EmpyrPartner::billingAccount()->search(['query' => 'A Name', 'numResults' => 100]);`
* `$data = EmpyrPartner::billingAccount(['billing_account' => 12345])->links();`
* `$data = EmpyrPartner::billingAccount()->add([
    'account.name' => 'Bill Van Pelt',
    'account.accountingEmail' => 'name@domain.com',
    'account.paymentMethod' => 'MANUAL',
    'address.postalCode' => '12345',
]);`
* `$data = EmpyrPartner::billingAccount(['billing_account' => 12345])->link(['business' => 12345]);`
* `$data = EmpyrPartner::billingAccount(['billing_account' => 12345])->update(['account.name' => 'Test Van Pelt']);`

### Cards
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->card(['card' => 12345]);`
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->list();`
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->add([
'cardNumber' => '1111111111111111',
'expirationMonth' => 3,
'expirationYear' => 2022,
]);`
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->delete(['card' => 12345]);`
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->deleteByNumber(['cardNumber' => 1111111111111111]);`
* `$data = Empyr::card( ['email' => 'name@domain.com'] )->setPrimary(['card' => 12345]);`

### Devices

### Fundraisers
* `$data = Empyr::fundraiser(['fundraiser' => 123])->fundraiser();`
* `$data = Empyr::fundraiser()->search(['query' => 'Denver']);`
* `$data = Empyr::fundraiser(['fundraiser' => 123])->donations();`
* `$data = Empyr::fundraiser(['fundraiser' => 123])->summary();`
* `$data = Empyr::fundraiser(['fundraiser' => 123])->userFundraiserTotals();`
* `$data = Empyr::fundraiser(['fundraiser' => 123, 'email' => 'linusx@gmail.com'])->join();`
* `$data = Empyr::fundraiser(['fundraiser' => 123, 'email' => 'linusx@gmail.com'])->leave();`

### Invoices
* `$data = EmpyrPartner::invoice(['invoice' => 12345])->invoice();`
* `$data = EmpyrPartner::invoice()->lookup(['startDate' => '2019/10/1', 'state' => 'POSTED']);`
* `$data = EmpyrPartner::invoice(['invoice' => 12345])->adjustments();`
* `$data = EmpyrPartner::invoice(['invoice' => 12345])->invoiceTransactions();`
* `$data = EmpyrPartner::invoice(['invoice' => 12345])->transactions();`
* `$data = EmpyrPartner::invoice(['invoice' => 12345])->collect();`

### Metros
* `$data = Empyr::metro(['metro' => 1])->metro();`
* `$data = Empyr::metro()->list();`
* `$data = Empyr::metro(['metro' => 1])->summary();`
* `$data = Empyr::metro(['metro' => 1])->topBusinesses();`
* `$data = Empyr::metro(['metro' => 1])->topUsers();`

### Offers
* `$data = Empyr::offer()->add(['business' => 12345, 'requiresActivation' => true, 'minPurchaseAmount' => 5]);`
* `$data = Empyr::offer(['offer' => 12345])->update(['endDate' => '2020/11/1']);`

### Reports
* `$data = Empyr::report()->statsReport(['business' => 12345, 'groupingOption' => 'DAY_OF_MONTH']);`
* `$data = Empyr::report()->txReport(['business' => 12345]);`

### Subscriptions
* `$data = EmpyrPartner::subscription(['subscription' => 12345])->subscription();`
* `$data = EmpyrPartner::subscription()->add(['business' => 12345, 'plan' => 123]);`

### Utilities
* `$data = EmpyrPartner::utility()->categories();`
* `$data = EmpyrPartner::utility()->extendedCategories();`
* `$data = EmpyrPartner::utility()->features();`
* `$data = EmpyrPartner::utility()->info();`
* `$data = EmpyrPartner::utility()->locationSuggestions(['query' => 'kale']);`
* `$data = EmpyrPartner::utility()->searchSuggestions(['query' => 'kale']);`
* `$data = EmpyrPartner::utility()->searchSuggestionsMap(['query' => 'kale']);`
