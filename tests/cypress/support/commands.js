// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })


 //const getCompareSnapshotsPlugin = require('cypress-visual-regression/dist/plugin');
const compareSnapshotCommand = require('cypress-image-diff-js/dist/command');
compareSnapshotCommand();

/**
 * Logs out the user.
 */
Cypress.Commands.add('drupalLogout', () => {
  cy.visit('/user/logout');
})

/**
 * Basic user login command. Requires valid username and password.
 *
 * @param {string} username
 *   The username as which to log in.
 * @param {string} password
 *   The password for the user's account.
 */
Cypress.Commands.add('loginAs', (username, password) => {
  cy.drupalLogout();

  cy.visit('/user/login');
  cy.get('#edit-name')
    .type(username);
  cy.get('#edit-pass').type(password, {
    log: false,
  });
  cy.get('#edit-submit').click();
});

/**
 * Defines a cypress command that executes drush commands.
 *
 * Note that our definition of the drush command depends on our environment
 * variable 'drushCommand'. Define this in cypress.json or cypress.env.json
 * based on your local dev setup.
 *
 * We're passing the object containing 'failOnNonZeroExit' to Cypress so that
 * our Cypress tests don't crash if the drush command returns an error (e.g.
 * if we try to delete a user account that does not exist.)
 */
Cypress.Commands.add('drush', (command, args = [], options = {}) => {
  return cy.exec(`${Cypress.env('drushCommand')} ${command} ${stringifyArguments(args)} ${stringifyOptions(options)} -y`, { failOnNonZeroExit: false });
});

/**
 * Returns a series of arguments, separated by spaces.
 *
 * @param {*} args
 * @returns
 */
function stringifyArguments(args) {
  return args.join(' ');
}

/**
 * Returns a string from an array of options.
 *
 * @param {array} options
 * @returns
 */
function stringifyOptions(options) {
  return Object.keys(options).map(option => {
    let output = `--${option}`;

    if (options[option] === true) {
      return output;
    }

    if (options[option] === false) {
      return '';
    }

    if (typeof options[option] === 'string') {
      output += `="${options[option]}"`;
    } else {
      output += `=${options[option]}`
    }

    return output;
  }).join(' ')
}

/**
 * Creates a user account and assigns to it an array of roles.
 *
 * @param {string} username
 *   The username for the new account.
 * @param {array} roles
 *   An array of roles to be assigned to the new account.
 */
Cypress.Commands.add('createUserWithRole', (username, roles = []) => {
  // Cancel the account with username, if it exists.
  Cypress.log({
    message: `Canceling ${username} account, if it exists.`,
  });
  cy.drush('user:cancel', ['--delete-content', username]);

  // Create the user account and assign to it the roles from the 2nd parameter.
  Cypress.log({
    message: `Create: ${username}`,
  });
  return cy.drush('user:create', [username])
    .its('stderr')
    /** An example of an assertion testing the result of a drush command. */
    // .should('match', /Created a new user/gm)
    .then(function (stderr) {
      const uid = stderr.match(/Created a new user with uid ([0-9]+)/)[1]
      cy.log(uid);
      roles.forEach(role => {
        cy.drush('user:role:add', [role, username])
        // Another example of an assertion testing the result of a drush
        // command.
        // .its('stdout')
        // .should('match', /\[success\] Added/gm);
        Cypress.log({
          message: `assign ${role} role`,
        });
      });
      cy.wrap({
        username,
        uid,
      });
    });
});

/**
 * Logs a user in by their uid via drush uli.
 */
Cypress.Commands.add('loginUserByUid', (uid) => {
  cy.drush('user-login', [], { uid, uri: Cypress.env('baseUrl') })
    .its('stdout')
    .then(function (url) {
      cy.visit(url);
    });
});

/**
 * Logs a user in by their username via drush uli.
 */
Cypress.Commands.add('loginUserByUsername', (username) => {
  cy.drush('user-login', [], { name: username, uri: Cypress.env('baseUrl') })
    // cy.drush('user-login', ['--name=' + username, '--uri=' + `${Cypress.env('baseUrl')}`])
    .its('stderr')
    .then(function (url) {
      cy.visit(url);
    });
});

