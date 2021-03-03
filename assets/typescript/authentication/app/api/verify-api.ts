export type ErrorPayload = {
  code: string;
  message: string;
}

export type ConfirmedPayload = {
  confirmed: boolean;
}

export type VerifyResponse = {
  success: boolean;
  payload: ConfirmedPayload;
  error: ErrorPayload|undefined;
}

export default class VerifyAPI {
  private readonly  url: string;

  constructor(url: string) {
    this.url = url;
  }

  public fetchStatus(): Promise<VerifyResponse> {
    return new Promise((resolve, reject) => {
      const request = new XMLHttpRequest();
      request.open('POST', this.url);
      request.onload = function () {
        if (this.status >= 200 && this.status < 300) {
          const data = <VerifyResponse>JSON.parse(request.response);
          return resolve(data);
        }
        reject(this.status);
      };
      request.onerror = function () {
        reject(this.status);
      };

      request.send();
    });
  }
}