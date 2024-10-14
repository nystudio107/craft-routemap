import {defineConfig} from 'vitepress'

export default defineConfig({
  title: 'Route Map Plugin',
  description: 'Documentation for the Route Map plugin',
  base: '/docs/route-map/v1/',
  lang: 'en-US',
  head: [
    ['meta', {content: 'https://github.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://twitter.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://youtube.com/nystudio107', property: 'og:see_also',}],
    ['meta', {content: 'https://www.facebook.com/newyorkstudio107', property: 'og:see_also',}],
  ],
  themeConfig: {
    socialLinks: [
      {icon: 'github', link: 'https://github.com/nystudio107'},
      {icon: 'twitter', link: 'https://twitter.com/nystudio107'},
    ],
    logo: '/img/plugin-logo.svg',
    editLink: {
      pattern: 'https://github.com/nystudio107/craft-routemap/edit/develop/docs/docs/:path',
      text: 'Edit this page on GitHub'
    },
    algolia: {
      appId: 'AE3HRUJFEW',
      apiKey: 'c5dcc2be096fff3a4714c3a877a056fa',
      indexName: 'routemap',
      searchParameters: {
        facetFilters: ["version:v1"],
      },
    },
    lastUpdatedText: 'Last Updated',
    sidebar: [],
    nav: [
      {text: 'Home', link: 'https://nystudio107.com/plugins/routemap'},
      {text: 'Store', link: 'https://plugins.craftcms.com/route-map'},
      {text: 'Changelog', link: 'https://nystudio107.com/plugins/routemap/changelog'},
      {text: 'Issues', link: 'https://github.com/nystudio107/craft-routemap/issues'},
      {
        text: 'v1', items: [
          {text: 'v5', link: 'https://nystudio107.com/docs/route-map/'},
          {text: 'v4', link: 'https://nystudio107.com/docs/route-map/v4/'},
          {text: 'v1', link: '/'},
        ],
      },
    ]
  },
});
