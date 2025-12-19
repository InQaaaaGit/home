/**
 * vim: et ts=2 sts=2 sw=2
 *
 * @module     local_cdo_unti2035bas/local/fd_controller
 */
/* eslint-disable jsdoc/require-jsdoc */

import Ajax from 'core/ajax';
import Notification from 'core/notification';


async function streamFDDelete(streamId, extensionName) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_stream_fd_delete',
      args: {
        streamid: streamId,
        extensionname: extensionName,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

async function factdefExtensionDelete(factdefId, extensionName) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_factdef_extension_delete',
      args: {
        factdefid: factdefId,
        extensionname: extensionName,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

async function factDelete(factId) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_fact_delete',
      args: {
        factid: factId,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

async function factExtensionDelete(factId, extensionName) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_fact_extension_delete',
      args: {
        factid: factId,
        extensionname: extensionName,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

export default {
  streamFDDelete,
  factdefExtensionDelete,
  factDelete,
  factExtensionDelete,
};
