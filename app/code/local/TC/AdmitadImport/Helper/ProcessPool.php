<?php

/**
 * @category   TC
 * @package    TC_AdmitadImport
 * @author     Alexandr Smaga <smagaan@gmail.com>
 */
class TC_AdmitadImport_Helper_ProcessPool extends Mage_Core_Helper_Abstract
    implements TC_AdmitadImport_Logger_LoggerAwareInterface
{
    const MAX_TASK_RUNTIME = 3600;

    /** @var TC_AdmitadImport_Logger_LoggerInterface */
    private $_logger;

    /** @var array */
    private $_runningTasks = array();

    /** @var string */
    private $_php = null;

    /**
     * Inject the logger
     *
     * @param TC_AdmitadImport_Logger_LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(TC_AdmitadImport_Logger_LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Getter for logger
     *
     * @return TC_AdmitadImport_Logger_LoggerInterface
     */
    protected function _getLogger()
    {
        return $this->_logger;
    }

    /**
     * Run async import processes
     */
    public function run()
    {
        $startTime      = time();
        $maxPoolRuntime = 5 * 3600; // 5h
        $maxJobs        = 10;
        $idleTime       = 30; // 30s
        $maxIdleRun     = 10;
        $this->_runTasks($startTime, $maxPoolRuntime, $idleTime, $maxJobs, $maxIdleRun);
    }

    /**
     * Starts process pool manager in another process
     *
     * @return \Symfony\Component\Process\Process
     */
    public function startProcessPool()
    {
        return $this->_runProcess(sprintf('%s -- pool', $this->_getImportBinary()));
    }

    /**
     * Spawn tasks in multiple processes
     *
     * @param int $startTime  When process management was started
     * @param int $maxRuntime Max time to run
     * @param int $idleTime   Time to wait for pending jobs
     * @param int $maxJobs    Max concurrent jobs count
     * @param int $maxIdleRun Max allowed idle runs
     *
     * @return int
     */
    private function _runTasks($startTime, $maxRuntime, $idleTime, $maxJobs, $maxIdleRun)
    {
        $idle = 0;

        while ((time() - $startTime < $maxRuntime) && $idle < $maxIdleRun) {
            $this->_checkRunningTasks();

            while (count($this->_runningTasks) < $maxJobs) {
                $pendingTask = $this->_runAsyncTask();

                if (null === $pendingTask) {
                    sleep($idleTime);
                    $idle++;
                    continue 2; // Check if the maximum runtime has been exceeded.
                }

                sleep(1);
                $this->_checkRunningTasks();
            }

            sleep(2);
        }

        if (count($this->_runningTasks) > 0) {
            while (count($this->_runningTasks) > 0) {
                $this->_checkRunningTasks();
                sleep(2);
            }
        }

        return 0;
    }

    /**
     * Check if there is pending file, and if it exists run async task
     *
     * @return \Symfony\Component\Process\Process|null
     */
    protected function _runAsyncTask()
    {
        $filename = $this->_getPendingFile();

        if (null === $filename) {
            return null;
        }

        $command = sprintf('%s -- image --filename "%s"', $this->_getImportBinary(), $filename);
        $process = $this->_runProcess($command);
        $this->_getLogger()->log(
            sprintf('Task PID: %d. Started  for filename', $process->getPid(), $filename)
        );

        $this->_runningTasks[] = array(
            'task'      => $process,
            'startTime' => time()
        );

        return $process;
    }

    /**
     * Tic check for every running task
     */
    private function _checkRunningTasks()
    {
        foreach ($this->_runningTasks as $idx => $data) {
            /** @var \Symfony\Component\Process\Process $task */
            $task = $data['task'];
            if ($task->isRunning() && ((time() - $data['startTime']) > self::MAX_TASK_RUNTIME)) {
                $this->_getLogger()->log(
                    sprintf('Max execution time exceed for process PID: %d. Terminating...', $task->getPid()),
                    Zend_Log::ALERT
                );

                $task->stop(5);
            } elseif ($task->isTerminated()) {
                $this->_getLogger()->log(
                    sprintf('Task PID: %d. Finished with exit code: %d', $task->getPid(), $task->getExitCode())
                );
            } else {
                continue;
            }

            unset($this->_runningTasks[$idx]);
        }
    }

    /**
     * Run async process
     *
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    private function _runProcess($command)
    {
        $logger = $this->_getLogger();

        $process = new \Symfony\Component\Process\Process($command);
        $process->start(
            function ($type, $data) use ($logger) {
                if ($logger) {
                    /** @var TC_AdmitadImport_Logger_LoggerInterface $logger */
                    $logger->log(
                        $data, \Symfony\Component\Process\Process::ERR == $type ? Zend_Log::ERR : Zend_Log::INFO
                    );
                }
            }
        );

        return $process;
    }

    /**
     * Find pending files, returns full path if exists
     *
     * @return string|null
     */
    private function _getPendingFile()
    {
        $importDir = Mage::getBaseDir('media') . DS . 'import' . DS;
        if (is_dir($importDir)) {
            $iterator       = new DirectoryIterator($importDir);
            $regExpIterator = new RegexIterator(
                $iterator, sprintf('#.*\.%d#i', TC_AdmitadImport_Helper_Images::STATUS_PENDING)
            );
            /** @var \SplFileInfo $file */
            foreach ($regExpIterator as $file) {
                if ($file->isFile()) {
                    return $file->getRealPath();
                }
            }
        }

        return null;
    }

    /**
     * Finds PHP executable command
     *
     * @return string
     */
    private function _getPhp()
    {
        if ($this->_php === null) {
            $finder     = new \Symfony\Component\Process\PhpExecutableFinder();
            $this->_php = $finder->find();
        }

        return $this->_php;
    }

    /**
     * Returns import command binary file path
     *
     * @return string
     */
    private function _getImportBinary()
    {
        return sprintf(
            '%s %s%sshell%simport.php',
            $this->_getPhp(),
            rtrim(Mage::getBaseDir('base'), DS),
            DS,
            DS
        );
    }
}
