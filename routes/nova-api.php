<?php


Route::get('studies', 'StudyListController@handle');
Route::post('studies/{study}/select', 'StudySelectionController@handle');
Route::get('sample-data/{dataSample}', 'SampleDataController@handle');
Route::post('samples/{sample}/report', 'SampleReportController@downloadLink');
Route::get('samples/{sample}/report/{experiment}/download', 'SampleReportController@download')->name('report-download');
Route::get('assays/{assay}/reagents', 'AssayRelatedReagentController@handle');


Route::get('import-template/{resource}', 'ImportTemplateController@show');
Route::get('import-template/{resource}/download', 'ImportTemplateController@download');

Route::get('storageable', 'storageController@storageable');
Route::get('storage/{sampleType}', 'StorageController@index');


Route::get('results/{assay}', 'ResultController@index');
Route::get('results/{assay}/targets', 'ResultController@targets');
Route::get('results/{assay}/request-for-download', 'ResultController@requestForDownload');
Route::get('results/{assay}/download', 'ResultController@download')->name('download-results');