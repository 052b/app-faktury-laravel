<?php

namespace App\Http\Controllers;

use App\Http\Requests\GusRequest;
use GusApi\SearchReport;
use Illuminate\Http\Request;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\ReportTypes;
use GusApi\BulkReportTypes;

class ClientGusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GusRequest $request)
    {
        $gus = new GusApi('b12566f3780846d2907a');

        try {
            $validated  = $request->validated();
            $nipToCheck = preg_replace('/[^0-9]/', '', $validated['nip']);
            $gus->login();

            $gusReports = $gus->getByNip($nipToCheck);

            foreach ($gusReports as $gusReport) {
                if ($gus->getMessageCode() === 0) {
                    return $this->getResponse($gusReport);
                }
            }
        } catch (InvalidUserKeyException $e) {
            logger('GUS - Bad user key');
        } catch (NotFoundException $e) {
            logger('GUS - No data found');
            logger($gus->getResultSearchMessage());
        }

        return response()->json(['error' => $gus->getResultSearchMessage()], 404);
    }

    private function getResponse(SearchReport $report): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'name'     => $report->getName(),
            'address'  => $this->getFormattedAddress($report),
            'address2' => '',
            'zip'      => $report->getZipCode(),
            'city'     => $report->getCity(),
        ]);
    }

    private function getFormattedAddress(SearchReport $report): string
    {
        $address = $report->getStreet();

        if ($report->getPropertyNumber()) {
            $address .= ' ' . $report->getPropertyNumber();
        }

        if ($report->getApartmentNumber()) {
            $address .= '/' . $report->getApartmentNumber();
        }

        return $address;
    }
}
