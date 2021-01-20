<?php

namespace RafflesArgentina\RestfulController\Traits;

use Lang;

trait FormatsResponseMessages
{
    /**
     * Format the store failed message.
     *
     * @param string $message The Exception message.
     *
     * @return string
     */
    public function storeFailedMessage($message = null)
    {
        if (Lang::has('restful-controller.storefailed')) {
            return trans('restful-controller.storefailed', ['message' => $message]);
        }

        return 'Operation failed while creating a new record with message: '.$message;
    }

    /**
     * Format the update failed message.
     *
     * @param string $number  The model id.
     * @param string $message The Exception message.
     *
     * @return string
     */
    public function updateFailedMessage($number, $message = null)
    {
        if (Lang::has('restful-controller.updatefailed')) {
            return trans(
                'restful-controller.updatefailed', [
                    'number' => $number,
                    'message' => $message
                ]
            );
        }

        return 'Operation failed while updating the record '.$number.' with message: '.$message;
    }

    /**
     * Format the destroy failed message.
     *
     * @param string $number  The model id.
     * @param string $message The Exception message.
     *
     * @return string
     */
    public function destroyFailedMessage($number, $message = null)
    {
        if (Lang::has('restful-controller.destroyfailed')) {
            return trans(
                'restful-controller.destroyfailed', [
                    'number' => $number,
                    'message' => $message
                ]
            );
        }

        return 'Operation failed while destroying the record '.$number.' with message: '.$message;
    }

    /**
     * Format the store successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function storeSuccessfulMessage($number)
    {
        if (Lang::has('restful-controller.storesuccessful')) {
            return trans(
                'restful-controller.storesuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Newly created record number: '.$number;
    }

    /**
     * Format the update successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function updateSuccessfulMessage($number)
    {
        if (Lang::has('restful-controller.updatesuccessful')) {
            return trans(
                'restful-controller.updatesuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Register successfully updated: '.$number;
    }

    /**
     * Format the destroy successful message.
     *
     * @param string $number The model id.
     *
     * @return string
     */
    public function destroySuccessfulMessage($number)
    {
        if (Lang::has('restful-controller.destroysuccessful')) {
            return trans(
                'restful-controller.destroysuccessful', [
                    'number' => $number
                ]
            );
        }

        return 'Register successfully deleted: '.$number;
    }
}
