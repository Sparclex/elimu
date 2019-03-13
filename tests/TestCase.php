<?php

namespace Tests;

use App\Models\Study;
use App\Policies\Authorization;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Testing\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function resource($file)
    {
        return base_path('tests/resources/' . $file);
    }

    public function signInAsAdministrator()
    {
        return $this->signIn(true);
    }

    /**
     * @param bool $isAdmin
     * @param Study|null $study
     * @param int $power
     * @return User
     */
    public function signIn($isAdmin = false, Study $study = null, $power = Authorization::SCIENTIST)
    {
        $this->be($user = factory(User::class)
            ->create(['is_admin' => $isAdmin]));

        if ($study) {
            $user->studies()->attach($study, compact('power'));
            $user->study()->associate($study);
        }

        return $user;
    }

    public function signInManager(Study $study = null)
    {
        $study = $study ?? factory(Study::class)->create();
        return $this->signIn(false, $study, Authorization::LABMANAGER);
    }

    public function signInScientist(Study $study = null)
    {
        $study = $study ?? factory(Study::class)->create();
        return $this->signIn(false, $study);
    }

    public function signInMonitor(Study $study = null)
    {
        $study = $study ?? factory(Study::class)->create();
        return $this->signIn(false, $study, Authorization::MONITOR);
    }

    public function novaDelete($uri, $resourceIds, $params = [])
    {
        $resourceIds = !is_array($resourceIds) ? [$resourceIds] : $resourceIds;

        return $this->deleteJson($uri, array_merge([
            'resources' => $resourceIds
        ], $params));
    }

    public function createTmpFile($filename)
    {
        $file = tmpfile();
        fwrite($file, $this->stubContent($filename));
        fseek($file, 0);
        return new File($filename, $file);
    }

    public function stubContent($filename)
    {
        return file_get_contents(__DIR__ . '/stubs/' . $filename);
    }
}
