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

    class FieldScanImportDefinition extends ImportDefinition
    {
        public function __construct()
        {
            parent::__construct(
                'field_scan',
                'field_scan',
                [
                    new AutoGuidColumn($this, 'id'),
                    new DateTimeColumn($this, 'time_stamp', ['ordinal' => 0, 'name' => '']),
                    new TextColumn($this, 'pallet_id', ['ordinal' => 1, 'name' => '']),
                    new LookupColumn($this, 'area_id', ['ordinal' => 2, 'name' => '']),
                    new LookupColumn($this, 'module_type_id', ['ordinal' => 3, 'name' => '']),
                    new PointColumn($this, 'location', ['ordinal' => 4, 'name' => '']),
                    new ImageDataColumn($this, 'image_1', ['ordinal' => 5, 'name' => '']),
                    new ImageDataColumn($this, 'image_2', ['ordinal' => 6, 'name' => '']),
                    new ImageDataColumn($this, 'image_3', ['ordinal' => 7, 'name' => '']),
                    new LookupColumn($this, 'field_scan_type_id', ['ordinal' => 8, 'name' => '']),
                    new TextColumn($this, 'notes', ['ordinal' => 9, 'name' => '']),
                ]
            );

            $this->image_folder = '/home/xeeleeah/subdomains/bnm.xeelee.org/public_html/import_images';

            $this->lookups = [
                'area_id' => fn () => Query::fetchLookup('select `id`, `label` from `area`'),
                'module_type_id' => fn () => Query::fetchLookup('select `id`, `label` from `module_type`'),
                'field_scan_type_id' => fn () => Query::fetchLookup('select `id`, `label` from `field_scan_type`'),
            ];
        }
    }
}
