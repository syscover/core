<?php

return [

    //******************************************************************************************************************
    //***   Date pattern
    //***   Pattern     Description                                             Values
    //***   d           Day of the month, 2 digits with leading zeros	        01 to 31
    //***   m           Numeric representation of a month, with leading zeros   01 through 12
    //***   Y           A full numeric representation of a year, 4 digits       Examples: 1999 or 2015
    //***   y           A two digit representation of a year                    Examples: 99 or 03
    //***
    //***   To get all patterns see  http://php.net/manual/en/function.date.php
    //***
    //******************************************************************************************************************
    'datePattern'               => 'd-m-Y',

    //******************************************************************************************************************
    //***   Set fields to save from EXIT image properties to avoid utf-8 characters.
    //***   That they are includes by software like Photoshop
    //******************************************************************************************************************
    'exif_fields_allowed'       => [
        'FileName',
        'FileDateTime',
        'FileSize',
        'FileType',
        'MimeTye',
        'SectionsFound',
        'COMPUTED',
        'ImageWidth',
        'ImageLength',
        'BitsPerSample',
        'PhotometricInterpretation',
        'ImageDescription',
        'Orientation',
        'SamplesPerPixel',
        'XResolution',
        'YResolution',
        'ResolutionUnit',
        'Software',
        'DateTime',
        'Title',
        'Comments',
        'Keywords'
    ]
];