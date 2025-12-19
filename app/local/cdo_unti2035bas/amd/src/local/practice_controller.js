/**
 * vim: et ts=2 sts=2 sw=2
 *
 * @module     local_cdo_unti2035bas/local/practice_controller
 */
/* eslint-disable jsdoc/require-jsdoc */

import Ajax from 'core/ajax';
import Notification from 'core/notification';


async function practiceDiaryDelete(practiceDiaryId) {
  try {
    await Promise.all(Ajax.call([{
      methodname: 'local_cdo_unti2035bas_practice_diary_delete',
      args: {
        practicediaryid: practiceDiaryId,
      },
    }]));
  } catch(exc) {
    Notification.exception(exc);
  }
}

export default {
  practiceDiaryDelete,
};
