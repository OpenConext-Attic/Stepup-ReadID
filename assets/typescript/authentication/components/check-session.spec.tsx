import React from 'react';
import {render, screen, act, cleanup} from '@testing-library/react';
import CheckSession  from './check-session';
import VerifyAPI, {VerifyResponse} from "../app/api/verify-api";

describe("Element should poll verification state", () => {

  const counterTest = async (duration: number, retry: number) =>{

    // Test polling steps
    const expectedValue = retry + '/2,' + duration + '/3';
    const stateText = await screen.findByTestId("check-session-polling");
    expect(stateText.textContent).toMatch(expectedValue);

    act(() => {
      jest.advanceTimersByTime(1000);
    })
  }

  it("renders", async () => {
    render(
      <CheckSession
        verifyUrl="https://readid.stepup.example.com/verify"
        authenticationUrl="https://readid.stepup.example.com/authenticate"
        retryInSeconds={2}
        maxTries={3}
        timeOutMessage="Unable to process verification"
      />,
    );

    const stateText = await screen.findByTestId("check-session-polling");
    expect(stateText.textContent).toMatch("0/2,0/3");

     cleanup();
  })

  it("polls verify api when not confirmed", async () => {

    jest.useFakeTimers();

    // Mock VerifyAPI
    jest.mock('../app/api/verify-api');
    const fetchStatus = jest.fn();
    fetchStatus.mockResolvedValue(
      JSON.parse(`{"success": true,"payload":{"confirmed":false}}`) as VerifyResponse
    );
    VerifyAPI.prototype.fetchStatus = fetchStatus;

    render(
      <CheckSession
        verifyUrl="https://readid.stepup.example.com/verify"
        authenticationUrl="https://readid.stepup.example.com/authenticate"
        retryInSeconds={2}
        maxTries={3}
        timeOutMessage="Unable to process verification"
      />,
    );

    const tests = [
      [0, 0], [0, 1],
      [1, 0], [1, 1],
      [2, 0], [2, 1],
      [2, 2]
    ];

    expect.assertions(tests.length+1);

    const counterTest = async (duration: number, retry: number) =>{

      // Test polling steps
      const expectedValue = retry + '/2,' + duration + '/3';
      const stateText = await screen.findByTestId("check-session-polling");
      expect(stateText.textContent).toMatch(expectedValue);

      act(() => {
        jest.advanceTimersByTime(1000);
      })
    }

    for (let i = 0; i < tests.length; i++) {
      await counterTest(tests[i][0], tests[i][1]);
    }

    // Test final timeout
    const expectedValue = "Unable to process verification";
    const stateText = await screen.findByText(expectedValue);
    expect(stateText.textContent).toMatch(expectedValue);

    cleanup();
  })


  it("redirects to authentication url when confirmed", async () => {

    jest.useFakeTimers();

    // Mock VerifyAPI
    jest.mock('../app/api/verify-api');
    const fetchStatus = jest.fn();
    fetchStatus.mockResolvedValue(
      JSON.parse(`{"success": true,"payload":{"confirmed":true}}`) as VerifyResponse
    );
    VerifyAPI.prototype.fetchStatus = fetchStatus;

    // Mock window.location
    jest.spyOn(window.location, 'assign').mockImplementation( (l:string) => {
      expect(l).toEqual("https://readid.stepup.example.com/authenticate");
    })

    render(
      <CheckSession
        verifyUrl="https://readid.stepup.example.com/verify"
        authenticationUrl="https://readid.stepup.example.com/authenticate"
        retryInSeconds={2}
        maxTries={3}
        timeOutMessage="Unable to process verification"
      />,
    );

    const tests = [
      [0, 0], [0, 1],
      [0, 2]
    ];

    expect.assertions(tests.length+1);

    for (let i = 0; i < tests.length; i++) {
      await counterTest(tests[i][0], tests[i][1]);
    }

    cleanup();
  })
})
