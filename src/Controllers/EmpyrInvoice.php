<?php
/**
 * invoice Controller.
 */

namespace Linusx\Empyr\Controllers;

use App\Http\Controllers\LuckyDiem\MoglController;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Linusx\Empyr\Exceptions\EmpyrMissingRequiredFields;
use Linusx\Empyr\Exceptions\EmpyrNotPublisherCredentials;
use Linusx\Empyr\Facades\EmpyrPublisher;

/**
 * Class EmpyrInvoice.
 */
class EmpyrInvoice extends EmpyrController
{
    /**
     * Empyr Invoices Methods.
     *
     * @param array $data Data to set field with.
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     * @throws EmpyrNotPublisherCredentials
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Allows lookup up of an invoice by id.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/invoice
     *
     * @options
     * * invoice	**required** The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function invoice($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice']);
    }

    /**
     * Allows lookup up of invoices given criteria.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/lookup
     *
     * @options
     * * startDate	Include only invoices after this start date
     * * endDate	Include only invoices before this end date
     * * business	Include only invoices for the given business
     * * account	Include only invoices for the given account
     * * state	Include only invoices in the given state [PENDING, POSTED, COMPLETED, FAILED, PAST_DUE]
     * * offset	The offset into the results
     * * numResults	The number of results to retrieve (default 100)
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function lookup($options = [])
    {

        // Filter options.
        $options = $this->allowedKeys($options, ['startDate', 'endDate', 'business', 'account', 'state', 'offset', 'numResults']);

        return $this->callAPI('invoices/', $options);
    }

    /**
     * Get all adjustments given the invoice.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/adjustments
     *
     * @options
     * * invoice **required** The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function adjustments($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callAPI('invoices/'.$options['invoice'].'/adjustments', $options);
    }

    /**
     * Get all transactions run on a given an invoice.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/invoiceTransactions
     *
     * @options
     * * invoice **required** The invoice id.
     *
     * @param array $options
     * @param int $page
     * @param int $per_page
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function invoiceTransactions($options = [], $page = 1, $per_page = 100)
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        $offset = ($page - 1) * $per_page;
        $options['numResults'] = $per_page;
        $options['offset'] = $offset;

        return $this->callAPI('invoices/'.$options['invoice'].'/invoiceTransactions', $options);
    }

    /**
     * Get all the transactions that were run to bill this invoice.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/transactions
     *
     * @options
     * * invoice **required** The invoice id.
     *
     * @param array $options
     * @param int $page
     * @param int $per_page
     * @return bool|mixed
     * @throws EmpyrMissingRequiredFields
     * @throws GuzzleException
     */
    public function transactions($options = [], $page = 1, $per_page = 100) {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        $offset = ($page - 1) * $per_page;
        $options['numResults'] = $per_page;
        $options['offset'] = $offset;

        return $this->callAPI('invoices/'.$options['invoice'].'/transactions', $options);
    }

    public function allTransactions($invoice_id, $page = 1, $per_page = 500) {

        try {
            $first = $this->transactions(['invoice' => $invoice_id], $page, $per_page);
        }
        catch (EmpyrMissingRequiredFields $e) { return $this->setError( $e->getMessage() ); }
        catch (GuzzleException $e) { return $this->setError( $e->getMessage() ); }

        if ( empty( $first ) || $first->isError() ) {
            return $this->getError();
        }

        $first_transactions = $first->get();

        $all_transactions = $first_transactions['transactions']->results;

        $total_pages = ceil($first_transactions['transactions']->hits / $per_page);

        for( $i = 2; $i <= $total_pages; $i++) {
            try {
                $transactions = $this->transactions(['invoice' => $invoice_id], $i, $per_page);
            }
            catch (EmpyrMissingRequiredFields $e) { continue; }
            catch (GuzzleException $e) { continue; }

            if ( empty( $transactions ) || $transactions->isError() ) {
                continue;
            }

            if ( empty( $transactions->get()['transactions'] ) || empty( $transactions->get()['transactions']->results ) ) {
                continue;
            }

            $all_transactions = array_merge_recursive($all_transactions, array_values( $transactions->get()['transactions']->results ) );
        }

        return $all_transactions;
    }

    public function makeCSV( $start_date , $cached = true ) {
        $reports = new EmpyrReport();
        $start = date('m/01/Y', strtotime( $start_date ) );
        $end = date('m/t/Y', strtotime( $start_date ) );
        $limit = 200000;

        $key = 'get-invoices-' . $limit . '-' . strtotime( $start ) . '-' . strtotime( $end );
        $invoices = true === $cached ? Cache::get( $key ) : false;
        $invoices = false;

        if ( empty( $invoices ) ) {
            $invoices = $this->lookup(['startDate' => $start, 'state' => ['PENDING,POSTED'], 'numResults' => 2000])->get();
            Cache::put( $key, $invoices, now()->addMinutes( 3600 ) );
        }

        if ( false === $invoices ) {
            return $this->setError( 'No invoices found.' );
        }

        $venues = true === $cached ? Cache::get( 'get-invoices-venues-' . $key, [] ) : false;
        $venues = false;
        if ( empty( $venues ) ) {
            foreach ( $invoices['results'] as $invoice ) {
                if ( $invoice->total > 0 ) {
                    $venues[ $invoice->business->id ][ $invoice->id ]['invoice_id'] = $invoice->id;
                    $venues[ $invoice->business->id ][ $invoice->id ]['business_name'] = $invoice->business->name;
                    $venues[ $invoice->business->id ][ $invoice->id ]['business_address'] = $invoice->business->address;
                    $venues[ $invoice->business->id ][ $invoice->id ]['start_date'] = date('m-d-Y', $invoice->startDate );
                    $venues[ $invoice->business->id ][ $invoice->id ]['end_date'] = date( 'm-d-Y', $invoice->endDate );
                    $venues[ $invoice->business->id ][ $invoice->id ]['invoice_total'] = $invoice->total;
                    $venues[ $invoice->business->id ][ $invoice->id ]['amount_spent'] = 0;
                    $venues[ $invoice->business->id ][ $invoice->id ]['total_cash_back'] = 0;
                    $venues[ $invoice->business->id ][ $invoice->id ]['total_cash_back_billed'] = 0;
                    $venues[ $invoice->business->id ][ $invoice->id ]['total_referral_fee'] = 0;
                    $venues[ $invoice->business->id ][ $invoice->id ]['state'] = $invoice->state;
                    $venues[ $invoice->business->id ][ $invoice->id ]['invoice'] = $invoice;

                    $transactions = $reports->txReport(
                        [
                            'byProcessDate'  => true,
                            'groupingOption' => 'MONTH_OF_YEAR',
                            'sortingOption'  => 'GROUPED_BY_VALUE',
                            'business'       => $invoice->business->id
                        ]
                    );

                    if ( $this->isError() || empty( $transactions ) || false === $transactions ) {
                        Log::info( 'Empty transactions for ' . $invoice->id . ' and  ' . $invoice->business->name );
                        Log::info( $this->getError() );
                        continue;
                    }

                    $search_date = date('Y-m-01', $invoice->startDate );
                    foreach ( $transactions->data->results->results as $transaction ) {

                        if ( $search_date === $transaction->groupedByValue ) {
                             $venues[ $invoice->business->id ][ $invoice->id ]['num_transactions']        = $transaction->numTransactions;
                             $venues[ $invoice->business->id ][ $invoice->id ]['amount_spent']           = round( $transaction->revenue, 2);
                             $venues[ $invoice->business->id ][ $invoice->id ]['total_cash_back']        = round( $transaction->cashback, 2);
                             $venues[ $invoice->business->id ][ $invoice->id ]['total_cash_back_billed'] = round( $transaction->cashbackBilled, 2);
                             $venues[ $invoice->business->id ][ $invoice->id ]['total_referral_fee']     = round( $transaction->referralFee, 2);
                         }
                    }
                }
            }
            Cache::put( 'get-invoices-venues-' . $key, $venues, now()->addMinutes( 3600 ) );
        }

        if ( empty( $venues ) ) {
            return $this->setError( 'No valid invoices found for ' . $start );
        }

        $fp = fopen('/tmp/luckydiem-' . date('y-m-d', strtotime( $start_date ) ) . '.csv', 'w');

        fputcsv($fp, [
            'Business ID',
            'Invoice ID',
            'Business Name',
            'Start Date',
            'End Date',
            'Number of Transactions',
            'Invoice Total',
            'Amount Spent',
            'Cash Back',
            'Cash Back Billed',
            'Referral Fee',
            'Address',
            'City',
            'State',
            'Zip Code',
            'Invoice State',
        ]);

        foreach ( $venues as $business_id => $the_venues ) {
            foreach ( $the_venues as $invoice_id => $venue ) {
                fputcsv($fp, [
                    $business_id,
                    $venue['invoice_id'],
                    $venue['business_name'],
                    $venue['start_date'],
                    $venue['end_date'],
                    $venue['num_transactions'],
                    $venue['invoice_total'],
                    $venue['amount_spent'],
                    $venue['total_cash_back'],
                    $venue['total_cash_back_billed'],
                    $venue['total_referral_fee'],
                    $venue['business_address']->streetAddress,
                    $venue['business_address']->city,
                    $venue['business_address']->state,
                    $venue['business_address']->postalCode,
                    $venue['state'],
                ]);

                $transactions = $venue['transactions'] ?? false;
                if ( empty( $transactions ) ) {
                    continue;
                }

                $fp_invoice = fopen('/tmp/' . $invoice_id . '.csv', 'w');
                fputcsv($fp_invoice, [
                    'Business Name',
                    'Business ID',
                    'Transaction ID',
                    'Cash Back Amount',
                    'Cash Back Billed',
                    'Referral Fee',
                    'Transaction Date',
                ]);

                foreach ( $transactions as $transaction ) {
                    fputcsv($fp_invoice, [
                        $transaction->venue->name,
                        $transaction->venue->id,
                        $transaction->id,
                        $transaction->cashbackAmount,
                        $transaction->cashbackBilled,
                        $transaction->referralFee,
                        date('m-d-Y h:ia', $transaction->dateOfTransaction),
                    ]);
                }
                fclose($fp_invoice);
            }
        }
        fclose($fp);

        return true;
    }

    /**
     * Attempts collection of the invoice.
     *
     * @mogl
     * https://www.mogl.com/api/docs/v2/Invoices/collect
     *
     * @options
     * * invoice **required** The invoice id.
     *
     * @param array $options
     * @return bool|mixed
     * @throws GuzzleException
     * @throws EmpyrMissingRequiredFields
     */
    public function leave($options = [])
    {
        if (empty($options['invoice']) && empty($this->invoice)) {
            throw new EmpyrMissingRequiredFields('No invoice id given.');
        }

        if (empty($options['invoice'])) {
            $options['invoice'] = $this->invoice;
        }

        // Filter options.
        $options = $this->allowedKeys($options, ['invoice']);

        return $this->callUserAPI('invoices/'.$options['invoice'].'/collect', $options, 'post');
    }
}
