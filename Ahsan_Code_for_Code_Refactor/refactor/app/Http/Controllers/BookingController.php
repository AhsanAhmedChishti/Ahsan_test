<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

class BookingController extends Controller
{
    protected $repository;

    public function __construct(BookingRepository $bookingRepository)
    {
        // Inject the BookingRepository instance into the class
        $this->repository = $bookingRepository;
    }

    public function index(Request $request)
    {
        try {
            // Check if the 'user_id' parameter exists in the request
            if ($user_id = $request->get('user_id')) {
                // If 'user_id' exists, fetch jobs for that user
                $response = $this->repository->getUsersJobs($user_id);
            } elseif ($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')) {
                // If the authenticated user is an admin or superadmin, fetch all jobs
                $response = $this->repository->getAll($request);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }

            // Return a success response with the fetched records and a message
            return $this->successResponse($response, 'Record fetched successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function show($id)
    {
        try {
            // Find the job with the given ID and eager load the 'translatorJobRel.user' relationship
            $job = $this->repository->with('translatorJobRel.user')->find($id);
            
            if (!$job) {
                // If the job is not found, return an error response with status code 406
                return $this->errorResponse('Job not found', 406);
            }
            
            // Return a success response with the fetched job and a message
            return $this->successResponse($job, 'Record fetched successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function store(Request $request)
    {
        $authenticateUser = $request->__authenticatedUser;
        try {
            if($authenticateUser) {
                // Get all the request data
                $data = $request->all();
                // Store the job using the authenticated user and the request data
                $response = $this->repository->store($authenticateUser, $data);
                // Return a success response with the stored record and a message
                return $this->successResponse($response, 'Record stored successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function update($id, Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Get the authenticated user from the request
            $cuser = $request->__authenticatedUser;
            if($cuser) {
                // Update the job with the given ID using the data and the authenticated user
                $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);
                // Return a success response with the updated record and a message
                return $this->successResponse($response, 'Record updated successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function immediateJobEmail(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Store the job email using the request data
            $response = $this->repository->storeJobEmail($data);
            
            // Return a success response with the response from storing the job email and a message
            return $this->successResponse($response, 'Process completed successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function getHistory(Request $request)
    {
        try {
            // Check if the 'user_id' parameter exists in the request
            if ($user_id = $request->get('user_id')) {
                // If 'user_id' exists, fetch the user's job history using the user ID and the request
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                // Return a success response with the fetched records and a message
                return $this->successResponse($response, 'Record fetched successfully', 200);
            }
            
            // If 'user_id' doesn't exist, return an empty success response with a message indicating successful history fetching
            return $this->successResponse([], 'History fetched successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function acceptJob(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Get the authenticated user from the request
            $user = $request->__authenticatedUser;
            if($user) {
                // Accept the job using the request data and the authenticated user
                $response = $this->repository->acceptJob($data, $user);
                // Return a success response with the response from accepting the job and a message
                return $this->successResponse($response, 'Job accepted successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
            
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try {
            // Get the 'job_id' parameter from the request
            $data = $request->get('job_id');
            // Get the authenticated user from the request
            $user = $request->__authenticatedUser;
            if($user) {
                // Accept the job with the given ID using the 'job_id' and the authenticated user
                $response = $this->repository->acceptJobWithId($data, $user);
                // Return a success response with the response from accepting the job and a message
                return $this->successResponse($response, 'Accepted job with ID fetched successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
            
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function cancelJob(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Get the authenticated user from the request
            $user = $request->__authenticatedUser;
            if($user) {
                // Cancel the job using the request data and the authenticated user
                $response = $this->repository->cancelJobAjax($data, $user);
                // Return a success response with the response from canceling the job and a message
                return $this->successResponse($response, 'Job ended successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function endJob(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // End the job using the request data
            $response = $this->repository->endJob($data);
            // Return a success response with the response from ending the job and a message
            return $this->successResponse($response, 'Job ended successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function customerNotCall(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Perform the customer not call process using the request data
            $response = $this->repository->customerNotCall($data);
            // Return a success response with the response from the process and a message
            return $this->successResponse($response, 'Process completed successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function getPotentialJobs(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            // Get the authenticated user from the request
            $user = $request->__authenticatedUser;
            if($user) {
                // Retrieve the potential jobs for the authenticated user
                $response = $this->repository->getPotentialJobs($user);
                // Return a success response with the retrieved jobs and a message
                return $this->successResponse($response, 'Record fetched successfully', 200);
            } else {
                // If the request is invalid, return an error response with status code 406
                return $this->errorResponse('Invalid request', 406);
            }
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function distanceFeed(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            
            // Extract the necessary data from the request, with fallback values if not present
            $distance = isset($data['distance']) && $data['distance'] != "" ? $data['distance'] : "";
            $time = isset($data['time']) && $data['time'] != "" ? $data['time'] : "";
            $jobid = isset($data['jobid']) && $data['jobid'] != "" ? $data['jobid'] : "";
            $session = isset($data['session_time']) && $data['session_time'] != "" ? $data['session_time'] : "";
            $flagged = $data['flagged'] == 'true' ? 'yes' : 'no';
            $manually_handled = $data['manually_handled'] == 'true' ? 'yes' : 'no';
            $by_admin = $data['by_admin'] == 'true' ? 'yes' : 'no';
            $admincomment = isset($data['admincomment']) && $data['admincomment'] != "" ? $data['admincomment'] : "";
            
            // Update the distance and time fields in the Distance model if either distance or time is present
            if ($time || $distance) {
                $affectedRows = Distance::where('job_id', '=', $jobid)->update(['distance' => $distance, 'time' => $time]);
            }

            // Update the admin_comments, flagged, session_time, manually_handled, and by_admin fields in the Job model if any of them are present
            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
                $affectedRows1 = Job::where('id', '=', $jobid)->update(['admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin]);
            }

            // Return a success response with an empty array and a message
            return $this->successResponse([], 'Distance feed fetched successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function reopen(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();
            
            // Call the 'reopen' method on the repository and pass the data
            $response = $this->repository->reopen($data);

            // Return a success response with the response data and a message
            return $this->successResponse($response, 'Record reopened successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function resendNotifications(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();

            // Find the job based on the jobid
            $job = $this->repository->find($data['jobid']);

            // Convert the job to data
            $job_data = $this->repository->jobToData($job);

            // Send notifications to the translators
            $this->repository->sendNotificationTranslator($job, $job_data, '*');

            // Return a success response with an empty array and a message
            return $this->successResponse([], 'Push sent successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

    public function resendSMSNotifications(Request $request)
    {
        try {
            // Get all the request data
            $data = $request->all();

            // Find the job based on the jobid
            $job = $this->repository->find($data['jobid']);

            // Convert the job to data
            $job_data = $this->repository->jobToData($job);

            // Send SMS notification to the translator
            $this->repository->sendSMSNotificationToTranslator($job);

            // Return a success response with an empty array and a message
            return $this->successResponse([], 'SMS sent successfully', 200);
        } catch (\Exception $e) {
            // If an exception occurs, return an error response with the exception message and status code 406
            return $this->errorResponse($e->getMessage(), 'Error', 406);
        }
    }

}
