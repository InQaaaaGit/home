/**
 * vim: et ts=2 sts=2 sw=2
 * @module local_cdo_unti2035bas/loading
 */
/* eslint-disable jsdoc/require-jsdoc */

const reg = {};

function loading_deco(cb_start, cb_stop) {
  let counter = 0;
  return func => async function(...args) {
    try {
      // eslint-disable-next-line
      counter || cb_start();
      counter++;
      return await func.call(this, ...args);
    } finally {
      // eslint-disable-next-line
      counter && counter--;
      // eslint-disable-next-line
      counter || cb_stop();
    }
  };
}

function callback_start(id) {
  reg[id].listeners.forEach(obj => {
    if (typeof obj.startLoading === 'function') {
      obj.startLoading();
    }
  });
}

function callback_stop(id) {
  reg[id].listeners.forEach(obj => {
    if (typeof obj.stopLoading === 'function') {
      obj.stopLoading();
    }
  });
}

function get_loading_deco(id, listener=null) {
  if(!(id in reg)) {
    reg[id] = {
      listeners: new Set(),
      deco: loading_deco(() => callback_start(id), () => callback_stop(id)),
    };
  }

  if (listener) {
    reg[id].listeners.add(listener);
  }
  return reg[id].deco;
}

export {
  loading_deco,
  get_loading_deco,
};
