<?php


Route::get('studies', 'StudyListController@handle');
Route::post('studies/{study}/select', 'StudySelectionController@handle');

Route::get('import-template/{resource}', 'ImportTemplateController@show');
Route::get('import-template/{resource}/download', 'ImportTemplateController@download');

Route::get('storageable', 'StorageController@storageable');
Route::get('storage/{sampleType}', 'StorageController@index');


Route::get('/{resource}/filters/options', 'DependentFilterController@options');

Route::get('definition-files/{resultType}', 'DefinitionFileController@template');