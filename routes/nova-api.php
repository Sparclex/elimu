<?php


Route::get('studies', 'StudyListController@handle');
Route::post('studies/{study}/select', 'StudySelectionController@handle');
Route::get('sample-data/{dataSample}', 'SampleDataController@handle');
Route::post('samples/{sample}/report', 'SampleReportController@downloadLink');
Route::get('samples/{sample}/report/{experiment}/download', 'SampleReportController@download')->name('report-download');
Route::get('assays/{assay}/reagents', 'AssayRelatedReagentController@handle');
Route::get('result-overview/{assay}', 'ResultOverviewController@handle');
