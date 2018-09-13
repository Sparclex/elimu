<?php

Route::post('import-samples', 'SampleBatchImportController@handle');
Route::get('studies', 'StudyListController@handle');
Route::get('sample-data/{dataSample}', 'SampleDataController@handle');
