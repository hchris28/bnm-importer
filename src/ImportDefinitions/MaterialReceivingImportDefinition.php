<?php

namespace Bnm\Importer\ImportDefinitions {
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\IntegerColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\TextColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\DatetimeColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\AutoGuidColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\ImageDataColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\PointColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\LookupColumn;
    use Bnm\Importer\ImportDefinitions\ColumnDefinitions\PalletIdOCRColumn;
    use \Bnm\Importer\DataAccess\Query;

    class MaterialReceivingImportDefinition extends ImportDefinition
    {
        public function __construct()
        {
            parent::__construct(
                'material_receiving',
                'material_receiving_log',
                [
                    new AutoGuidColumn($this, 'id'),
                    new DateTimeColumn($this, 'time_stamp', ['ordinal' => 0, 'name' => '']),
                    new TextColumn($this, 'bill_of_lading', ['ordinal' => 1, 'name' => '']),
                    new TextColumn($this, 'material_receiving_report', ['ordinal' => 2, 'name' => '']),
                    new TextColumn($this, 'container_id', ['ordinal' => 3, 'name' => '']),
                    new TextColumn($this, 'material_type', ['ordinal' => 4, 'name' => '']),
                    new PalletIdOCRColumn($this, 'pallet_ids', ['ordinal' => 5, 'name' => '']),
                    new IntegerColumn($this, 'pallet_quantity', ['ordinal' => 6, 'name' => '']),
                    new IntegerColumn($this, 'unit_quantity', ['ordinal' => 7, 'name' => '']),
                    new LookupColumn($this, 'material_status_id', ['ordinal' => 8, 'name' => '']),
                    new PointColumn($this, 'location', ['ordinal' => 9, 'name' => '']),
                    new ImageDataColumn($this, 'image_1', ['ordinal' => 10, 'name' => '']),
                    new ImageDataColumn($this, 'image_2', ['ordinal' => 11, 'name' => '']),
                    new ImageDataColumn($this, 'image_3', ['ordinal' => 12, 'name' => '']),
                    new ImageDataColumn($this, 'image_4', ['ordinal' => 13, 'name' => '']),
                    new ImageDataColumn($this, 'image_5', ['ordinal' => 14, 'name' => '']),
                    new ImageDataColumn($this, 'signature', ['ordinal' => 15, 'name' => ''])
                ]
            );

            $this->image_folder = '/home/xeeleeah/subdomains/bnm.xeelee.org/public_html/import_images';

            $this->lookups = [
                'material_status_id' => fn () => Query::fetchLookup('select `id`, `label` from `material_status`')
            ];
        }
    }
}
