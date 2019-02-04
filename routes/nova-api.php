<?php


Route::get('studies', 'StudyListController@handle');
Route::post('studies/{study}/select', 'StudySelectionController@handle');
Route::get('sample-data/{dataSample}', 'SampleDataController@handle');
Route::post('samples/{sample}/report', 'SampleReportController@downloadLink');
Route::get('samples/{sample}/report/{experiment}/download', 'SampleReportController@download')->name('report-download');
Route::get('assays/{assay}/reagents', 'AssayRelatedReagentController@handle');
Route::get('result-overview/{assay}', 'ResultOverview@index');


Route::get('import-template/{resource}', 'ImportTemplateController@show');
Route::get('import-template/{resource}/download', 'ImportTemplateController@download');

Route::get('storage/{sampleType}', 'StorageController@index');