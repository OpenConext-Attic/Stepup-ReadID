import React from 'react';
import ReactDOM from 'react-dom';
import QrCode from './components/qrcode';
import CheckSession from './components/check-session';

declare const image: string;
declare const timeOutMessage: string;

ReactDOM.render(
  <div>
    <QrCode image={image} />
    <CheckSession
      verifyUrl="/verify"
      authenticationUrl="/authentication"
      retryInSeconds={3}
      maxTries={40}
      timeOutMessage={timeOutMessage}
    />
  </div>,
  document.getElementById('root')
);
