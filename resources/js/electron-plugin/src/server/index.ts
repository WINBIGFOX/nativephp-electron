import startAPIServer, { APIProcess } from "./api.js";
import {
  retrieveNativePHPConfig,
  retrievePhpIniSettings,
  serveApp,
  startScheduler,
} from "./php.js";
import { appendCookie } from "./utils.js";
import state from "./state.js";

export async function startPhpApp() {
  const result = await serveApp(
    state.randomSecret,
    state.electronApiPort,
    state.phpIni
  );

  state.phpPort = result.port;

  await appendCookie();

  return result.process;
}

export function runScheduler() {
  startScheduler(state.randomSecret, state.electronApiPort, state.phpIni);
}

export function startAPI(): Promise<APIProcess> {
  return startAPIServer(state.randomSecret);
}

export { retrieveNativePHPConfig, retrievePhpIniSettings };
