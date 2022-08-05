<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VaccinationSupport;
use App\Models\VaccinationPoint;
use App\Models\Campaign;
use App\Models\CampaingPoint;
use App\Models\CampaingSupport;
use App\Models\Location\The\TheNeighborhood;
use App\Models\Location\The\TheNeighborhoodAlias;
use stdClass;

class VaccinationSupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaign = new Campaign();

        $campaign->year = '2021';
        $campaign->start = '2021-07-01';
        $campaign->goal = '5000';

        $campaign->save();

        $path = __DIR__ . '/files/the/vacination_point.csv';
        $file = file_get_contents($path, true);

        $delimiter = ',';
        $enclosure = '';

        if ($file) {
            $header = null;
            $datas = array();
            $rows = explode("\n", $file);

            foreach ($rows as $row) {
                $item = str_getcsv($row, $delimiter, $enclosure, "\n");
                $countItem = count($item);
                for ($i = 0; $i < $countItem; $i++) {
                    $item[$i] = trim($item[$i]);
                }

                if (!$header) {
                    $header = $item;
                    continue;
                }
                if (count($header) == count($item)) {
                    $datas[] = array_combine($header, $item);
                }
            }

            $vaccinationSupport = null;
            $campaignSupport = new stdClass();

            foreach ($datas as $data) {
                if ($data['type'] == 'a') {
                    $vaccinationSupport = new VaccinationSupport();
                    $vaccinationSupport->name = trim($data['name']);
                    $vaccinationSupport->address = trim($data['address']);
                    $vaccinationSupport->number = trim($data['number']);
                    $vaccinationSupport->address_complement = trim($data['reference_point']);
                    $data['neighborhood'] = trim($data['neighborhood']);

                    if (strlen($data['neighborhood']) >= 3) {
                        $neighborhoodAlias =
                        TheNeighborhoodAlias::getOrCreate($data['neighborhood']);

                        $vaccinationSupport
                        ->the_neighborhood_alias_id = $neighborhoodAlias->id;
                    }

                    $vaccinationSupport->save();

                    $campaignSupport = new CampaingSupport();
                    $campaignSupport->campaign_id = $campaign->id;
                    $campaignSupport->vaccination_support_id = $vaccinationSupport->id;
                    $campaignSupport->save();

                    continue;
                }

                $vaccinationPoint = new VaccinationPoint();
                $vaccinationPoint->name = $data['name'];
                $vaccinationPoint->address = $data['address'];
                $vaccinationPoint->number = $data['number'];
                $vaccinationPoint->address_complement = $data['reference_point'];
                $data['neighborhood'] = trim($data['neighborhood']);

                if (strlen($data['neighborhood']) >= 3) {
                    $neighborhoodAlias =
                    TheNeighborhoodAlias::getOrCreate($data['neighborhood']);

                    $vaccinationPoint
                    ->the_neighborhood_alias_id = $neighborhoodAlias->id;
                }

                $vaccinationPoint->save();

                $campaignPoint = new CampaingPoint();
                $campaignPoint->campaing_support_id = $campaignSupport->id;
                $campaignPoint->vaccination_point_id = $vaccinationPoint->id;
                $campaignPoint->save();
            }
        }
    }
}
