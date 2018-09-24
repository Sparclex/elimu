<?php

Route::post('import-samples', 'SampleBatchImportController@handle');
Route::get('studies', 'StudyListController@handle');
Route::get('sample-data/{dataSample}', 'SampleDataController@handle');
Route::post('samples/{sample}/report', 'SampleReportController@downloadLink');
Route::get('samples/{sample}/report/{experiment}/download', 'SampleReportController@download')->name('report-download');
