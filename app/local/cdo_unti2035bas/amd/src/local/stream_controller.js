/**
 * vim: et ts=2 sts=2 sw=2
 *
 * @module     local_cdo_unti2035bas/local/stream_controller;
 */
/* eslint-disable jsdoc/require-jsdoc */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

async function stream_sync(streamId) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_stream_sync',
      args: {
        streamid: streamId,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

export default {
    stream_sync,
};
