<?php

namespace Bnm\Importer\ImportDefinitions\ColumnDefinitions {
    abstract class ColumnValueType
    {
        const Int = 0;
        const Float = 1;
        const String = 2;
        const Date = 3;
        const Datetime = 4;
        const Boolean = 5;
        const Guid = 6;
        const ImageData = 7;
        const Point = 8;
    }
}
