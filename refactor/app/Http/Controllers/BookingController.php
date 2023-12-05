<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * Retrieve jobs based on the request parameters.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = null;

        // Check if 'user_id' is provided in the request
        if ($user_id = $request->get('user_id')) {
            // Retrieve jobs for a specific user
            $response = $this->repository->getUsersJobs($user_id);
        } elseif (
            // Check if the authenticated user has admin or superadmin role
            in_array(
                $request->__authenticatedUser->user_type,
                [env('ADMIN_ROLE_ID'), env('SUPERADMIN_ROLE_ID')]
            )
        ) {
            // Retrieve all jobs for admin or superadmin
            $response = $this->repository->getAll($request);
        }

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Display the specified job with related translator information.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieve the job with related translator information
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        // Return the job information as an HTTP response
        return response($job);
    }

    /**
     * Store a newly created job based on the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Retrieve all data from the request
        $data = $request->all();

        // Call the repository's store method to create a new job
        $response = $this->repository->store($request->__authenticatedUser, $data);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Update the specified job based on the provided request data.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        // Retrieve all data from the request
        $data = $request->all();
        
        // Retrieve the authenticated user from the request
        $cuser = $request->__authenticatedUser;

        // Call the repository's updateJob method to update the job with the given ID
        // Exclude unnecessary data such as '_token' and 'submit' from the request data
        $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Process and send email for an immediate job request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function immediateJobEmail(Request $request)
    {
        // Get the admin sender email from the configuration
        $adminSenderEmail = config('app.adminemail');

        // Retrieve all data from the request
        $data = $request->all();

        // Call the repository's storeJobEmail method to handle the immediate job email
        $response = $this->repository->storeJobEmail($data);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Get job history for a specific user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|null
     */
    public function getHistory(Request $request)
    {
        // Check if the 'user_id' parameter is present in the request
        if ($user_id = $request->get('user_id')) {

            // Call the repository's getUsersJobsHistory method to retrieve user's job history
            $response = $this->repository->getUsersJobsHistory($user_id, $request);

            // Return the response as an HTTP response
            return response($response);
        }

        // Return null if 'user_id' is not present in the request
        return null;
    }

    /**
     * Accept a job based on the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function acceptJob(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Get the authenticated user from the request
        $user = $request->__authenticatedUser;

        // Call the repository's acceptJob method to process job acceptance
        $response = $this->repository->acceptJob($data, $user);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Accept a job with the given ID for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function acceptJobWithId(Request $request)
    {
        // Get job ID from the request data
        $jobId = $request->get('job_id');

        // Get authenticated user from the request
        $user = $request->__authenticatedUser;

        // Use the repository to handle the job acceptance
        $response = $this->repository->acceptJobWithId($jobId, $user);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Cancel a job based on the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cancelJob(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Get the authenticated user from the request
        $user = $request->__authenticatedUser;

        // Call the repository's cancelJobAjax method to process job cancellation
        $response = $this->repository->cancelJobAjax($data, $user);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * End a job based on the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function endJob(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Call the repository's endJob method to process job ending
        $response = $this->repository->endJob($data);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Handle the scenario when the customer does not call.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function customerNotCall(Request $request)
    {
        // Get all data from the request
        $requestData = $request->all();

        // Call the repository's method to process the scenario
        $response = $this->repository->customerNotCall($requestData);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Get the potential jobs for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getPotentialJobs(Request $request)
    {
        // Get all data from the request
        $requestData = $request->all();

        // Get the authenticated user from the request
        $user = $request->__authenticatedUser;

        // Call the repository's method to retrieve potential jobs
        $response = $this->repository->getPotentialJobs($user);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Update distance, comments, and flags for a job based on the provided data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function distanceFeed(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Extract data from the request or set defaults
        $distance = $data['distance'] ?? "";
        $time = $data['time'] ?? "";
        $jobid = $data['jobid'] ?? "";
        $session = $data['session_time'] ?? "";

        // Check if flagged and set flagged status and admin comment
        if ($data['flagged'] == 'true') {
            if ($data['admincomment'] == '') {
                return response("Please, add comment");
            }
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }

        // Check if manually handled and set manually handled status
        $manually_handled = ($data['manually_handled'] == 'true') ? 'yes' : 'no';

        // Check if updated by admin and set the status
        $by_admin = ($data['by_admin'] == 'true') ? 'yes' : 'no';

        // Set admin comment or set it as an empty string
        $admincomment = $data['admincomment'] ?? "";

        // Update distance and time if provided
        if ($time || $distance) {
            $affectedRows = Distance::where('job_id', '=', $jobid)->update(['distance' => $distance, 'time' => $time]);
        }

        // Update job details if provided
        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            $affectedRows1 = Job::where('id', '=', $jobid)->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin,
            ]);
        }

        // Return a response indicating that the record has been updated
        return response('Record updated!');
    }


    /**
     * Reopen a job based on the provided data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function reopen(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Call the reopen method from the repository and get the response
        $response = $this->repository->reopen($data);

        // Return the response as an HTTP response
        return response($response);
    }

    /**
     * Resend notifications to the translator for a specific job.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resendNotifications(Request $request)
    {
        // Get all data from the request
        $data = $request->all();

        // Find the job based on the provided job ID
        $job = $this->repository->find($data['jobid']);

        // Convert the job data to a suitable format
        $job_data = $this->repository->jobToData($job);

        // Send notification to the translator for the specified job
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        // Return success response
        return response(['success' => 'Push sent']);
    }


    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
